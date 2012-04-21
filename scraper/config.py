import sys

import twiggy


log = twiggy.log
min_log_level = twiggy.levels.INFO
log_file = sys.stderr

try:
    from local_config import *
except ImportError:
    pass

twiggy.quickSetup(min_level=min_log_level, file=log_file)
