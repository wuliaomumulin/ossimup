#!/usr/bin/python
# -*- coding: UTF-8 -*-

import os
import sys
import yaml
import ConfigParser

import os
import sys
import ConfigParser
DEFAULT_CONF='/work/etc/agent/config.cfg'
DEFAULT_CONF_SETUP='/work/etc/framework/ossim_setup.conf'
DEFAULT_CONF_SERVER='/work/etc/server/config.xml'

def modify_configure(arg):
    cf = ConfigParser.ConfigParser()
    cf.read(DEFAULT_CONF)
    cf.set('plugin-defaults','sensor',arg)
    cf.set('asec','ip',arg)
    cf.set('control-framework','ip',arg)
    cf.set('output-idm','ip',arg)
    cf.set('output-server','ip',arg) 
    fp = open(DEFAULT_CONF,'w')
    cf.write(fp)
    fp.close()

    cmd = 'sed -i' +" 's/server_ip=.*/server_ip=" +arg + "/g' " + DEFAULT_CONF_SETUP
    os.system(cmd)
    
    cmd = 'sed -i' +" 's/admin_ip=.*/admin_ip=" +arg + "/g' " + DEFAULT_CONF_SETUP
    os.system(cmd)
    
    cmd = 'sed -i' +" 's/framework_ip=.*/framework_ip=" +arg +"/g' " + DEFAULT_CONF_SETUP
    os.system(cmd)
    
    cmd = 'sed -i ' +"  '/interfaces=/{n;s/ip=.*/ip="  + arg + "/;}' " + DEFAULT_CONF_SETUP
    os.system(cmd)
    
    cmd = 'sed -i ' + "'s/name=" +'"server" ip=.*/' + 'name="server" ip="' + arg +'" port="40003"\/>/g' +"' " + DEFAULT_CONF_SERVER
    #print cmd 
    os.system(cmd)

modify_configure(sys.argv[1])




