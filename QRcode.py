import cv2
import time
import pyzbar.pyzbar as pyzbar
from playsound import playsound
import pymysql
from gpiozero import LED
from time import sleep
red_led = LED(2)		// LED
orange_led = LED(3)
used_codes = []		// 사용된 데이터값을 저장하는 공간

cap = cv2.VideoCapture(0)
cap.set(3, 640)
cap.set(4, 480)			// 파이 카메라 캡처 화면 크기 설정
start = 0
while True:
    success, frame = cap.read()
    for code in pyzbar.decode(frame):
        #cv2.imwrite('qrbarcode_image.png', frame)//이미지 파일로 저장시도
        my_code = code.data.decode('utf-8')	//my_code를 utf-8형식으로 저장
        if my_code not in used_codes:	// 사용된 코드에 찍어온 데이터가 없을 경우
            start = time.time()		// 신규 QR코드	
            #print("인식 성공 : ", my_code)
            #playsound("qrbarcode_beep.mp3")// 인식 성공 후 비프음 출력하려 시도
            used_codes.append(my_code)	// 사용된 코드에 저장하여 중복방지
            
            conn = pymysql.connect(host='localhost', user = 'root', password = '1234', db = 'qr_code', charset='utf8')
            cur = conn.cursor()
                
            sql = "select * from user where name=%s"
            cur.execute(sql, my_code)
            result = cur.fetchall()
            conn.close()			// mysql DB에 카메라로 인식한 데이터저장
            
            if result:
                print('등록된 코드 입니다. {0}'.format(my_code))
 		 red_led.on()
                orange_led.off()
                sleep(5)
                red_led.off()			// 등록된 코드일 경우 빨간색 LED 점등
                break
            
            else:
                print('미등록 코드 입니다. {}'.format(my_code))
		 red_led.off()
                orange_led.on()
                sleep(5)
                orange_led.off()		//미등록 코드일 경우 주황색 LED 점등
                break
        elif my_code in used_codes:		// 인식한 데이터가 사용된 데이터일 경우
            if time.time() - start > 5:
                print("이미 인식된 코드 입니다.!!!")
                start = time.time()
                break
            else:
                pass
            
        else:
            pass
    cv2.imshow('QRcode Barcode Scan', frame)
    cv2.waitKey(1)
