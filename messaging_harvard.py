from twilio.rest import Client

f = open("keys.env", "r")
auth = f.read()

account_sid = 'ACbabcb857fb024ba4dd1b56b6c1d2be83'
auth_token = auth


client = Client(account_sid, auth_token)

def send_message(text, num):
    message = client.messages.create(
                                  messaging_service_sid='MG27e6d00de02f626d6c05040d1798c2e9',
                                  body="Someone is at your door -" + text,
                                  to= num
                              )
    print(message.sid)

