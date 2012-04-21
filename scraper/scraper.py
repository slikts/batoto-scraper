import urllib2
from sys import argv

import pymongo
from dateutil.parser import parse as parse_date
from xml.etree import ElementTree


class Scraper(object):
    feed_url = None
    connection = None
    db = None
    db_name = 'scraper'
    encoding = 'utf-8'

    def __init__(self, feed_url, connection):
        if feed_url:
            self.feed_url = feed_url
        self.connection = connection
        self.db = connection[self.db_name][self.feed_url]

    def scrape(self):
        items = self.db.items
        root = ElementTree.fromstring(urllib2.urlopen(self.feed_url).read())
        for raw_item in root.iter('item'):
            items.insert(self.read_item(raw_item))

    def read_item(self, raw_item):
        item = dict(
            _id=raw_item.find('guid').text,
            title=raw_item.find('title').text.encode(self.encoding),
            link=raw_item.find('link').text,
            date=parse_date(raw_item.find('pubDate').text)
        )
        return item


def main(feed_url, scraper_class=Scraper, connection=None):
    if not connection:
        connection = pymongo.Connection()
    scraper = scraper_class(feed_url, connection)
    scraper.scrape()


if __name__ == '__main__':
    feed_url = argv[1]
    if (len(argv) == 4):
        scraper_class = getattr(__import__(argv[2]), argv[3])
    else:
        scraper_class = Scraper
    main(feed_url, scraper_class)
