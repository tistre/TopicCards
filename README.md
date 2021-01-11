TopicBank (formerly XDDB)
=========================

TMDM experiments in PHP

![View Topic screenshot](screenshot_view_topic.png)

![Edit Topic screenshot](screenshot_edit_topic.png)

Install dependencies using Composer:

```
$ cd topicbank
$ docker run --rm --interactive --tty \
  --volume $PWD:/app \
  --volume ${COMPOSER_HOME:-$HOME/.composer}:/tmp \
  composer install
```
