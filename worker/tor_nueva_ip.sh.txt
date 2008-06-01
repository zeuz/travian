#!/usr/local/bin/expect -f
# telnet into tor and get clean path using expect

spawn telnet 127.0.0.1 9052
expect "Escape character is '^]'."
send "AUTHENTICATE\r"
expect "250 OK"
send "signal NEWNYM\r"
expect "250 OK"
send "quit\r"
# eos
