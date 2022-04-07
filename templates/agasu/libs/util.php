<?php

/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
class Util
{
    public static function getDateRusString($item): string
    {
        $day = date('d', strtotime($item->created));
        $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        $month = $months[date('m', strtotime($item->created)) - 1];
        $year = date('y', strtotime($item->created));
        return $day . ' ' . $month . ' ' . $year . ' года';
    }

    /**
     * @param String $string
     * @param $length
     * @param $encoding
     * @param $postfix
     *
     * @return string
     *
     * @since version
     */

    public static function getShortDescription(String $string, $length, $encoding, $postfix) {
        $tmp = mb_substr($string, 0, $length, $encoding);
        return mb_substr($tmp, 0, mb_strripos($tmp, ' ', 0, $encoding), $encoding) . $postfix;
    }

    /**
     * Получить список последних видео заданного плейлиста
     *
     * @param string $ytlist идентификатор плейлиста
     * @param int $cnt по сколько позиций обрабатывать (не всегда нужно содержимое всего плейлиста)
     * @param int $cache_life время жизни кеша в секундах (чтобы не получить бан IP за рилтайм запросы)
     * @return array список найденных видео, не более $cnt штук
     */
    public static function getYoutubePlaylistDataXml($ytlist, $cnt = 5, $cache_life = 3600) {
        # файл, содержащий копию ленты
        $cache_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . $ytlist . '.json';

        # Ключ для запросов
        $api_key = 'AIzaSyDaHljvY2Ftw_oEzaALYzNzJeNY7L_FBLc';

        # специальный адрес, отвечающий за выдачу фида
        $url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet'
            . '&playlistId=' . $ytlist
            . '&maxResults=' . $cnt
            . '&key=' . $api_key;

        # если кеш устарел...
        if (time() - @filemtime($cache_file) >= $cache_life) {
            # ...пытаемся обновить его
            $buf = file_get_contents($url);
            # в случае успеха запишем в файл обновлённые данные
            # проверка на пустоту нужна для того, чтобы не запороть кеш при ошибке
            if ($buf) file_put_contents($cache_file, $buf);
        }

        # если фид получить не удалось...
        if (empty($buf)) {
            # ...просто берём содержимое из кеша
            $buf = file_get_contents($cache_file);
        }

        # декодируем JSON данные
        $json = json_decode($buf, 1);

        $arr = array();

        # если данных нет — на выход
        if (empty($json['items'])) return $arr;

        # перебор доступных значений
        foreach ($json['items'] as $v) {
            $t = array(
                'title' => $v['snippet']['title'], # название
                'desc'  => $v['snippet']['description'], # описание
                'url'   => $v['snippet']['resourceId']['videoId'], # адрес
            );

            # изображения
            if (isset($v['snippet']['thumbnails'])) {
                $t['imgs']['all'] = array();
                foreach ($v['snippet']['thumbnails'] as $name => $item) {
                    $t['imgs']['all'][] = $item['url'];
                    $wh = $item['width'] . 'x' . $item['height'];
                    $t['imgs'][$wh][0] = $item['url'];
                }
            }

            $arr[] = $t;
        }

        return $arr;
    }

}