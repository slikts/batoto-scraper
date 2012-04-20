import re

import scraper


class BatotoScraper(scraper.Scraper):
    feed_url = 'http://www.batoto.net/recent_rss'
    title_re = re.compile(r'(?P<series>.+?) - (\w+) - '
    r'(:?Vol.(?P<volume>\d+) )?Ch.(?P<chapter>\w+?):?(?P<chapter_title>.+)')
    result_groups = ('volume', 'series', 'chapter', 'chapter_title')

    def parse_item(self, raw_item):
        item = super(BatotoScraper, self).parse_item(raw_item)
        results = self.title_re.match(item['title'])
        for group in self.result_groups:
            item[group] = results.group(group)
        return item


if __name__ == '__main__':
    scraper.main(None, scraper_class=BatotoScraper)
