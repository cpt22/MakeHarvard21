#!/usr/bin/env python3
import speech_recognition as sr
import subprocess
import os
import time
import threading
import signal
import RPi.GPIO as GPIO


DEV_ID = "1ZKJESFHM52KJJR88F78F"
OUTPUT_PATH = "/home/pi/MakeHarvard21/out/"
OUTPUT_FILETYPE = "wav"

BUTTON_PIN = 7
LED_PIN = 11

RECORDING_TIME = 5

def main():
    # Setup GPIO
    GPIO.setmode(GPIO.BOARD)
    GPIO.setwarnings(False)
    GPIO.setup(BUTTON_PIN, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)
    GPIO.add_event_detect(BUTTON_PIN, GPIO.RISING, bouncetime=500)
    GPIO.setup(LED_PIN, GPIO.OUT, initial=GPIO.LOW)

    thread = None

    while(True):
        if GPIO.event_detected(BUTTON_PIN):
            play_chime()
            if thread is None or not thread.is_alive():
                thread = create_recording()

    GPIO.cleanup()

def play_chime():
    pass

def create_recording():
    thread = recorder()
    thread.start()
    return thread


class recorder(threading.Thread):
    def __init__(self):
        threading.Thread.__init__(self)

    def run(self):
        print("Running recorder")
        filename = DEV_ID + time.strftime("-%d-%m-%Y--%H:%M:%S") + "." + OUTPUT_FILETYPE
        filepath = OUTPUT_PATH + filename

        proc_args = ['arecord', '-D', 'plughw:0', '-d', str(RECORDING_TIME), '-c1', '-r', '44100', '-f', 'S32_LE', '-t',
                     OUTPUT_FILETYPE, filepath]
        subprocess.Popen(proc_args, shell=False, preexec_fn=os.setsid)

        GPIO.output(LED_PIN, GPIO.HIGH)
        time.sleep(RECORDING_TIME + 1)
        GPIO.output(LED_PIN, GPIO.LOW)

        pw = processingWorker(filename + "processor", filename, filepath)
        pw.start()


class processingWorker(threading.Thread):
    def __init__(self, name, filename, filepath):
        threading.Thread.__init__(self)
        self.name = name
        self.filename = filename
        self.filepath = filepath

    def run(self):
        print("running Worker Thread")

        try:
            recognizer = sr.Recognizer()
            audio_file = sr.AudioFile(self.filepath)
            with audio_file as source:
                audio = recognizer.record(source)

            text_output = recognizer.recognize_google(audio)
            print(text_output)
        except:
            print("Whoops there was an issue")


if __name__ == "__main__":
    main()