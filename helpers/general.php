<?php
/**
 * Change/Add query string params ($_GET vars) in a URL
 *
 * @param string $url
 * @param array $vars
 * @return string
 */
function rewriteURLQuery($url, array $vars)
{
    $urlParts   = parse_url($url);

    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $query);
    } else {
        $query = [];
    }

    $query              = array_merge($query, $vars);
    $urlParts['query']  = http_build_query($query);

    return http_build_url($urlParts);
}

/**
 * Generate CRUD sorting URL
 *
 * @param string $orderBy
 * @return string
 */
function getSortURL($orderBy)
{
    return rewriteURLQuery(
        \Flight::request()->url,
        [
            'order_by'      => $orderBy,
            'order_type'    => \Flight::request()->query['order_type'] == 'asc' ? 'desc' : 'asc'
        ]
    );
}

/**
 * Quick and dirty Bootstrap pager widget.
 * It can be invoked directly into the template
 *
 * @param int $page Current page number
 * @param int $items Total items
 * @param int $perPage How many records should be displayed per page
 * @param int $maxLinks How many page links should pager display
 * @param bool $showFirst Show "First" button
 * @param bool $showLast Show "Last" button
 * @return string generated HTML widget
 */
function pager($page, $items, $perPage = 10, $maxLinks = 6, $showFirst = true, $showLast = true) {
    $url = \Flight::request()->url;
    $out = '';
    $totalPages = ceil($items / $perPage);

    if($items != 0 && $page >= 1 && $page <= $totalPages) {

        if($page >= $maxLinks) {
            $c = (floor($page / $maxLinks) * $maxLinks) - ($maxLinks - 1);
        }  else {
            $c = 1;
        }

        if($totalPages > 0 && $totalPages < $maxLinks) {
            $maxLinks = $totalPages;
        }

        if($totalPages != 1 && $page != 1 && $showFirst) {
            $out .= sprintf('<li><a href="%s">First</a></li>', rewriteURLQuery($url, ['page' => null]));
        }

        if($page != 1) {
            $out .= sprintf(
                '<li><a href="%s">&laquo;</a></li>',
                rewriteURLQuery($url, ['page' => $page > 2 ? $page - 1 : null])
            );
        }

        for ($i=1; $i <= $maxLinks; $i++) {
            if ($c <= $totalPages && $totalPages != 1) {
                $out .= sprintf(
                    '<li%s><a href="%s">%d</a></li>',
                    $page == $c ? ' class="active"' : '',
                    rewriteURLQuery($url, ['page' => $c]),
                    $c
                );
                $c++;
            }
        }
        if($page != $totalPages && $page < $totalPages) {
            $out .= sprintf(
                '<li><a href="%s">&raquo;</a></li>',
                rewriteURLQuery($url, ['page' => $page + 1])
            );
        }

        if($totalPages != 1 && $page != $totalPages && $showLast) {
            $out .= sprintf('<li><a href="%s">Last</a></li>', rewriteURLQuery($url, ['page' => $totalPages]));
        }
    }
    return $out ? '<ul class="pagination">'.$out.'</ul>' : '';
}

/**
 * UTF-8 sanitizing (escape all invalid UTF8 chars)
 *
 * @param string $value
 * @return string
 * @throws Exception
 */
function fixUTF8($value) {
    // No null bytes expected in our data, so let's remove it.
    $value = str_replace("\0", '', $value);

    static $useiconv;
    if ($useiconv === null) {
        $useiconv = (!function_exists('iconv') or @iconv('UTF-8', 'UTF-8//IGNORE', '100'.chr(130).'€') !== '100€');
    }

    if ($useiconv) {
        if (function_exists('mb_convert_encoding')) {
            $subst = mb_substitute_character();
            mb_substitute_character('');
            $result = mb_convert_encoding($value, 'utf-8', 'utf-8');
            mb_substitute_character($subst);
        } else {
            throw new Exception('Invalid byte sequence for encoding "UTF8":'.var_export($value, true));
        }
    } else {
        $result = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
    }

    return $result;
}

/**
 * @param array $data
 * @return array
 */
function sanitize(array $data)
{
    array_walk_recursive($data, function(&$item, $key) {
        $item = strip_tags($item);
        $item = fixUTF8($item);
        $item = trim($item);
    });

    return $data;
}

/**
 * Convert bytes to human readable representation
 * @param integer $size bytes
 * @return string
 */
function formatBytes($size)
{
    $unit   = ['B','KB','MB','GB','TB','PB'];
    return @round($size / pow(1024,($i = floor(log($size,1024))) ),2) . ' ' . $unit[$i];
}