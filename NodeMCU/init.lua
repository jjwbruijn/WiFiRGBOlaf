function bootup()
tmr.stop(0)
print("booting...")
dofile("network.lua")
dofile("gpio.lua")
startBlink(0,0,255,1500)
wifiConnect()
end

function setColor(string)
r, g, b = string.match(string, '(%d+),(%d+),(%d+)')
ws2812.write(string.char(g/10, r/10, b/10, g, r, b))
end

_, reset_reason = node.bootreason()
ws2812.init()
setColor("0,0,0")
if reset_reason == 0 then
bootup()
else 
print("We crashed..... Reset cause is "..reset_reason)
print("Booting in 5 seconds, enter tmr.stop(0) to abort")
    tmr.register(0,5000,tmr.ALARM_SEMI,bootup)
    tmr.start(0)
end

reset_reason=nil




