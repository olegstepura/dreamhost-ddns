@ECHO Off

set PUBLIC_URL=http://your.domain.com/ddns/
set PERSONAL_KEY=YOUR_PERSONAL_KEY
set LOG_PATH=%TEMP%/ddns.log

set URL=%PUBLIC_URL%/?key=%PERSONAL_KEY%
echo %URL%
for /f "tokens=*" %%v in ('wget -qO- %URL%') do (set DDNS_RESULT="%%v")

rem Replacing < and > chars
set DDNS_RESULT=%DDNS_RESULT:<=^^^^^^^<%
set DDNS_RESULT=%DDNS_RESULT:>=^^^^^^^>%

rem Replacing quotes we added earlier.
set DDNS_RESULT=%DDNS_RESULT:"=%

rem Form a log message
set OUT=[%date% %time%] %DDNS_RESULT%

rem Output
echo %OUT% >> %LOG_PATH%