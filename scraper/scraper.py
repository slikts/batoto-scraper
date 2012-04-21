import urllib2
from sys import argv
from xml.etree import ElementTree

import pymongo
from dateutil.parser import parse as parse_date

import config


class Scraper(object):
    feed_url = None
    connection = None
    db = None
    log = None
    db_name = 'scraper'
    encoding = 'utf-8'

    def __init__(self, feed_url, connection, log):
        if feed_url:
            self.feed_url = feed_url
        self.connection = connection
        self.log = log.name('scraper')
        self.db = connection[self.db_name][self.feed_url]

    def scrape(self):
        items = self.db.items
        self.log.fields(url=self.feed_url).debug('loading feed')
        root = ElementTree.fromstring(urllib2.urlopen(self.feed_url).read())
        all_items = root.find('channel').findall('item')
        self.log.fields(len=str(len(all_items))).debug('items found')
        for raw_item in all_items:
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
    scraper = scraper_class(feed_url, connection, config.log)
    scraper.scrape()


if __name__ == '__main__':
    feed_url = argv[1]
    if (len(argv) == 4):
        scraper_class = getattr(__import__(argv[2]), argv[3])
    else:
        scraper_class = Scraper
    main(feed_url, scraper_class)
