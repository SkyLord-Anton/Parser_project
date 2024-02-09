from bs4 import BeautifulSoup
import requests
import pymysql as mq
import datetime

http_proxy = "#####"
# Данные скрыты по просьбе заказчика
proxies = {
  			"https" :  http_proxy,
}

url = "https://cbr.ru/hd_base/KeyRate/"
request = requests.get(url, proxies=proxies)

soup = BeautifulSoup(request.text, "html.parser")
# Начинаем парсить
data = soup.find(string="Ставка").findNext('td')
for finalD in data:
    date = finalD
print(date)
date_time_obj = datetime.datetime.strptime(date, '%d.%m.%Y')
print('Дата:', date_time_obj.date())
date=date_time_obj.date()

keydata = soup.find(string="Ставка").findNext('td').findNext('td')
for finalK in keydata:
    key = finalK
print(key)
key=key.replace(',','.')

try:
    connection = mq.connect(
    host = "#",
    user = "#",
    password = "#",
    database = "#"
    )
    print("successfully connected...")
    print("#" * 20)
# Данные скрыты по просьбе заказчика
 
    cursor = connection.cursor()
    insert_query = "INSERT INTO cbrfkeyrate(DateStamp, Rate) VALUES ('{}', {});".format(date, key)
    print(insert_query)
    cursor.execute(insert_query)
    print(f"{cursor.rowcount} details inserted")
    connection.commit()
    connection.close()

except Exception as ex:
    print("Connection refused...")
    print(ex)
