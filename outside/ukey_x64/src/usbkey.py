import os
import getopt
import time
import sys
from ctypes import *

pp = os.path.split(os.path.realpath(__file__))[0]
pp = os.path.join(pp, "libusbkey.so")
usber = CDLL(pp)

myArr = create_string_buffer(b'\000',2048*2)
myArrLen = c_int(2048*2)
usber.GetUsbKeyInfo(myArr,myArrLen)

def main(pin):
    tt = 1
    if usber.VertifyUserPinCode(tt, pin) == 0:
        print "0"
    else:
        print "-1"

if __name__ == "__main__":
    try:
        opts, args = getopt.getopt(sys.argv[1:], "-h:-p:", ["help", "pin="])
        for key,value in opts:
            if key in ("-p", "--pin"):
                main(value)
    except getopt.GetoptError:
        print "python test.py --pin=123456"
