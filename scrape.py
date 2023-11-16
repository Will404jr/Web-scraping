from bs4 import BeautifulSoup
import requests
import csv

html_text = requests.get(
    "https://www.timesjobs.com/candidate/job-search.html?searchType=personalizedSearch&from=submit&txtKeywords=+Programmer&txtLocation="
).text

soup = BeautifulSoup(html_text, "lxml")

jobs = soup.find_all("li", class_="clearfix job-bx wht-shd-bx")

with open("_cms_scrape4.csv", "w", newline="", encoding="utf-8") as csv_file:
    csv_writer = csv.writer(csv_file)
    csv_writer.writerow(["Company Name", "Skills Required", "More Information"])

    for job in jobs:
        published_date = job.find("span", class_="sim-posted").span.text
        if "few" in published_date:
            company_name = job.find("h3", class_="joblist-comp-name").text.strip()
            skills = job.find("span", class_="srp-skills").text.strip()
            more_info = job.header.h2.a["href"]
            csv_writer.writerow([company_name, skills, more_info])
