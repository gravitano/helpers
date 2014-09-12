<?php

use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

if ( ! function_exists('get_random_filename'))
{
    /**
     * Get random filename with a specify file extension.
     *
     * @param $file
     * @return string
     */
    function get_random_filename($file)
    {
        return str_random() . '.' . $file->getClientOriginalExtension();
    }
}

if ( ! function_exists('a'))
{
    /**
     * Generate asset url with "assets" prefix.
     *
     * @param $url
     * @param bool $secure
     * @return string
     */
    function a($url, $secure = false)
    {
        return asset("assets/{$url}", $secure);
    }
}

if ( ! function_exists('r'))
{
    /**
     * Generate a url from the registered route.
     *
     * @param $route
     * @param array $parameters
     * @param string $prefix
     * @return string
     */
    function r($route, $parameters = array(), $prefix = 'admin')
    {
        return route("{$prefix}.{$route}", $parameters);
    }
}

if ( ! function_exists('option'))
{
    /**
     * Get a specify option from database.
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    function option($key, $default = null)
    {
        try
        {
            return Option::findByKey($key, $default);
        }
        catch (Exception $e)
        {
            return $default;
        }
    }
}

if ( ! function_exists('error_for'))
{
    /**
     * Get error message for the specified field and format it to "text-danger" style.
     *
     * @param $field
     * @param $errors
     * @return mixed
     */
    function error_for($field, $errors)
    {
        return $errors->first($field, '<div class="text-danger">:message</div>');
    }
}

if( ! function_exists('generate_slug'))
{
    /**
     * Get long date format (indonesian).
     *
     * @param $dateString
     * @return string
     */
    function indo_date($dateString)
    {
        list($year, $month, $date) = explode('-', $dateString);

        return $date . ' ' . Lang::get("helpers::months.{$month}") . ' ' . $year;
    }
}


if( ! function_exists('avatar_image'))
{
    /**
     * Get avatar image url.
     *
     * @param array $attributes
     * @return mixed
     */
    function avatar_image(array $attributes = [])
    {
        $user = Auth::user();

        if ($user->type == 'facebook')
        {
            $url = 'https://graph.facebook.com/' . $user->id_str . '/picture';
        }
        else
        {
            $url = $user->avatar;
        }

        $image = HTML::image($url, "Avatar {$user->name}", $attributes);

        return $image;
    }
}

if( ! function_exists('gravatar_url'))
{
    /**
     * Get gravatar image url.
     *
     * @param $email
     * @param int $size
     * @param string $default
     * @param string $rating
     * @return string
     */
    function gravatar_url($email, $size = 60, $default = 'mm', $rating = 'g')
    {
        return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . "?s={$size}&d={$default}&r={$rating}";
    }
}

if( ! function_exists('gravatar_image'))
{
    /**
     * Get gravatar image tag.
     *
     * @param $email
     * @param array $attributes
     * @param bool $cache
     * @param int $minutes
     * @return mixed
     *
     * @require "illuminate/html:4.*"
     */
    function gravatar_image($email, $attributes = array(), $cache = true, $minutes = 1140)
    {
        $secure = array_get($attributes, 'secure', false);
        $size = array_get($attributes, 'size', 60);
        $default = array_get($attributes, 'default', 'mm');
        $rating = array_get($attributes, 'rating', 'g');

        $attributes = array_except($attributes, ['secure', 'size', 'default', 'rating']);

        $image = HTML::image(gravatar_url($email, $size, $default, $rating), $email, $attributes, $secure);

        if ($cache === true)
        {
            return Cache::remember($email, $minutes, function () use ($image)
            {
                return $image;
            });
        }

        return $image;
    }
}

if( ! function_exists('set_download_header'))
{
    /**
     * Set new download header.
     *
     * @param $filename
     */
    function set_download_header($filename)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary ");
    }
}

if( ! function_exists('pagination_links'))
{
    /**
     * Flexible pagination links.
     *
     * @param Paginator $data
     * @return mixed
     */
    function pagination_links(Paginator $data)
    {
        if ($query = Request::query())
        {
            $query = array_except($query, 'page');

            return $data->appends($query)->links();
        }

        return $data->links();
    }
}

if( ! function_exists('upload_image'))
{
    /**
     * Upload image and auto create thumbnail.
     *
     * @param $field
     * @param $path
     * @param null $width
     * @param null $height
     * @return string
     *
     * @require "intervention/images:~2"
     */
    function upload_image($field, $path, $width = null, $height = null)
    {
        $file = Input::file($field);

        $filename = get_random_filename($file);

        if (!is_null($width) && !is_null($height))
        {
            $thumbnailPath = $path . '/thumbnail/';

            if (!File::isDirectory($thumbnailPath))
            {
                File::makeDirectory($thumbnailPath);
            }

            $filenPath = public_path($thumbnailPath . $filename);

            Image::make($file->getRealPath())
                ->resize($width, $height)
                ->save($filenPath);
        }

        $file->move($path, $filename);

        return $filename;
    }
}

if( ! function_exists('generate_slug'))
{
    /**
     * Generate slug url.
     *
     * @param array $data
     * @return string
     */
    function generate_slug(array $data)
    {
        return time() . '-' . Str::slug($data['title']);
    }
}