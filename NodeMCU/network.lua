hostname = "olaf"
mqttName = "olaf"
mqttTopic = "olaf/" -- change me!!
mqttconnected = 0
rate = 5000

function wifiConnect()
    print("Now connecting to wifi..")
    tmr.register(4, 1200000, tmr.ALARM_AUTO,wifiWD)
    tmr.start(4)
    wifi.sta.sethostname(hostname)
    enduser_setup.start(WifiUp,WifiError)
end

function WifiUp()
    setValue(0,255,0,nil, 600)
    print("wifi is up")
    tmr.stop(0)
    ConnectMQTT()
end

function WifiError()
    wifiConnect()
    startBlink(255,0,0,2000)
end

function wifiWD()
    if mqttconnected == 0 then
        print("WD expired")
        node.restart()
    end
end


function ConnectMQTT()
    m = mqtt.Client(mqttName, 30, "user", "password")
    m:lwt(mqttTopic.."status", "MEIN_LEBEN")
    m:on("message", MQTTMessage)
    m:on("offline", MQTTNotConnected)
    m:connect("192.168.0.2", 1883, 0, 0, MQTTConnected, MQTTNotConnected) -- change me!
end

function MQTTConnected()
    mqttconnected = 1
    m:subscribe(mqttTopic.."value", 0)
    m:subscribe(mqttTopic.."ping", 0)
    m:publish(mqttTopic.."status","connected",0,0)
    print("MQTT Connected")
    tmr.stop(0)
    setValue(255,255,255,nil, 600)
end

function MQTTNotConnected()
    startBlink(255,0,0,2000)
    mqttconnected = 0
    print("MQTT Disconnected...");
    tmr.register(0,12000,tmr.ALARM_SINGLE,ConnectMQTT)    
    tmr.start(0)
end

function MQTTMessage(client, topic, data)
    topic = string.sub(topic, -4)
    if topic=="alue" then
        parseString(data)
    end
    if topic=="ping" then
         client:publish(mqttTopic.."status","pong!",0,0)
    end 
end
