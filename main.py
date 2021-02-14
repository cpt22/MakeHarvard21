#!/usr/bin/env python3
import speech_recognition as sr
import subprocess
import os
import time
import threading
import RPi.GPIO as GPIO
from twilio.rest import Client
import requests

ENVIRONMENT_FILE = "keys.env"

DEVICE_ID = "ZKJESFHM52KJJR88F78FW3GH6"
OUTPUT_PATH = "/home/pi/MakeHarvard21/out/"
OUTPUT_FILETYPE = "wav"

BUTTON_PIN = 7
LED_PIN = 11

RECORDING_TIME = 15

MESSAGING_ENABLED = True

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
        filename = DEVICE_ID + time.strftime("-%d-%m-%Y--%H:%M:%S") + "." + OUTPUT_FILETYPE
        filepath = OUTPUT_PATH + filename

        proc_args = ['arecord', '-D', 'plughw:0', '-d', str(RECORDING_TIME), '-c1', '-r', '44100', '-f', 'S32_LE', '-t',
                     OUTPUT_FILETYPE, filepath]
        subprocess.Popen(proc_args, shell=False, preexec_fn=os.setsid)

        GPIO.output(LED_PIN, GPIO.HIGH)
        time.sleep(RECORDING_TIME)
        GPIO.output(LED_PIN, GPIO.LOW)
        time.sleep(1)

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

            wu = webUpload(self.filename, self.filepath, text_output)
            wu.start()

            tm = twilioMessenger("8472743667", text_output)
            tm.start()

            print(text_output)
        except:
            print("Whoops there was an issue")


class webUpload(threading.Thread):
    def __init__(self, filename, filepath, text):
        threading.Thread.__init__(self)
        self.filename = filename
        self.filepath = filepath
        self.text = text
        print(filename)

    def run(self):
        datas = {'filename': self.filename, 'device_id': DEVICE_ID, 'transcribed_text': self.text}
        files = {'recording': (open(self.filepath, 'rb'))}
        print(files)
        r = requests.post("http://192.168.100.111/bellringevent.php",
                          data=datas,
                          files=files)
        print(r.status_code)
        print(r.text)


class twilioMessenger(threading.Thread):
    def __init__(self, destination, text):
        threading.Thread.__init__(self)
        self.destination = destination
        self.text = text

    def run(self):
        print("running messenger")
        f = open(ENVIRONMENT_FILE, "r")
        auth = f.read()
        account_sid = 'ACbabcb857fb024ba4dd1b56b6c1d2be83'
        auth_token = auth
        client = Client(account_sid, auth_token)

        if MESSAGING_ENABLED:
            message = client.messages.create(
                messaging_service_sid='MG27e6d00de02f626d6c05040d1798c2e9',
                body="Someone is at your door - " + self.text,
                to=self.destination
            )
            print(message.sid)


if __name__ == "__main__":
    main()