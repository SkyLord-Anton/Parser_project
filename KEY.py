from bs4 import BeautifulSoup
import requests
import sqlite3
url = "https://cbr.ru/hd_base/KeyRate/"
request = requests.get(url)

soup = BeautifulSoup(request.text, "html.parser")
# Начинаем парсить
data = soup.find(string="Ставка").findNext('td')
for finalD in data:
    date = finalD

keydata = soup.find(string="Ставка").findNext('td').findNext('td')
for finalK in keydata:
    key = finalK

    # Начинаем работу с sqlite
Stavka = key
Data = date

with sqlite3.connect("database.db") as db:
    cursor = db.cursor()
# Values как список для будущего расширения базы данных, опционально (поэтому там "executemany"
    cursor.execute('''CREATE TABLE IF NOT EXISTS OurDatabase(id INTEGER PRIMARY KEY AUTOINCREMENT,
    Datestamp VARCHAR(30), Rate VARCHAR(30))''')
    values = [
        (Data, Stavka)
    ]
    cursor.executemany("INSERT INTO OurDatabase(Datestamp, Rate) VALUES(?, ?)", values)

#Костыльная версия удаления самой старой строки для экономии места НЕ ЗАПУСКАТЬ КОГДА СТРОК МЕНЬШЕ 2
   # cursor.execute("DELETE FROM OurDatabase WHERE id = (SELECT min(id) FROM OurDatabase)")