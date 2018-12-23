step = 0
nextfunc = nil
updateinterval = 10
updatestep = 1


function setValue(r, g, b, nextf, hz)
    tmr.stop(0);
    if hz ~= nil then
        setFreq(hz) 
    end
    nextfunc = nextf
    buffernew:fill(g, r, b)
    bufferold:replace(buffermix:dump())
    step = 0
    tmr.start(5)

end

function updateLeds()
    if step < 256 then
        buffermix:mix(step, buffernew, 256-step, bufferold)
        ws2812.write(buffermix)
        step = step + updatestep
        tmr.start(5)
    else
        if nextfunc then
            nextfunc()
        end
    end
 end

function setFreq(millhz)
    if millhz <= 300 then
        if(millhz<1) then
            millhz = 1
        end
        updateinterval = 1500/millhz
        updatestep = 1
    else
        updateinterval = 5
    end
    if millhz > 300 then
        updatestep = millhz / 300
    end
    
end

function parseString(string)
    blink,r, g, b, hz = string.match(string, '(%d+),(%d+),(%d+),(%d+),(%d+)')
    r = tonumber(r)
    g = tonumber(g)
    b = tonumber(b)
    hz = tonumber(hz)
    blink = tonumber(blink)
    if blink ~= 0 then
        startBlink(r,g,b,hz)
    else
        setValue(r,g,b,nil,hz)
    end
end 
    

function parseBlink(string)
    r, g, b, hz = string.match(string, '(%d+),(%d+),(%d+),(%d+)')
    r = tonumber(r)
    g = tonumber(g)
    b = tonumber(b)
    hz = tonumber(hz)
    startBlink(r,g,b,hz)
end

function parseColor(string)
    r, g, b, hz = string.match(string, '(%d+),(%d+),(%d+),(%d+)')
    r = tonumber(r)
    g = tonumber(g)
    b = tonumber(b)
    hz = tonumber(hz)
    setValue(r,g,b,nil,hz)
end

function startBlink(r, g, b, hz)
    if hz ~= nil then
        setFreq(hz) 
    end
    blinkr = r
    blinkg = g
    blinkb = b
    setValue(r,g,b,blinkOff)
end

function blinkOn()
    setValue(blinkr,blinkg,blinkb,blinkOff)
end

function blinkOff()
    setValue(0,0,0,blinkOn)
end

bufferold = ws2812.newBuffer(2, 3)
buffernew = ws2812.newBuffer(2, 3)
buffermix = ws2812.newBuffer(2, 3)
blinkr = 0
blinkg = 0
blinkb = 0
bufferold:fill(0,0,0)
buffermix:fill(0,0,0)
tmr.register(5,updateinterval,tmr.ALARM_SEMI, updateLeds)


