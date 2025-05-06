<?php

namespace Botble\Base\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\HtmlString model($model, array $options = [])
 * @method static \Illuminate\Support\HtmlString open(array $options = [])
 * @method static \Illuminate\Support\HtmlString hidden($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString input($type, $name, $value = null, $options = [])
 * @method static string getIdAttribute($name, $attributes)
 * @method static mixed|null getValueAttribute($name, $value = null)
 * @method static mixed|null old($name)
 * @method static \Illuminate\Support\HtmlString|string token()
 * @method static mixed getModel()
 * @method static void setModel($model)
 * @method static \Illuminate\Support\HtmlString close()
 * @method static \Illuminate\Support\HtmlString label($name, $value = null, $options = [], $escape_html = true)
 * @method static \Illuminate\Support\HtmlString text($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString password($name, $options = [])
 * @method static \Illuminate\Support\HtmlString range($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString search($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString email($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString tel($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString number($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString date($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString datetime($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString datetimeLocal($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString time($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString url($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString week($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString file($name, $options = [])
 * @method static \Illuminate\Support\HtmlString textarea($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString selectRange($name, $begin, $end, $selected = null, $options = [])
 * @method static \Illuminate\Support\HtmlString select($name, $list = [], $selected = null, array $selectAttributes = [], array $optionsAttributes = [], array $optgroupsAttributes = [])
 * @method static \Illuminate\Support\HtmlString getSelectOption($display, $value, $selected, array $attributes = [], array $optgroupAttributes = [])
 * @method static mixed|null selectYear()
 * @method static \Illuminate\Support\HtmlString selectMonth($name, $selected = null, $options = [], $format = '%B')
 * @method static \Illuminate\Support\HtmlString checkbox($name, $value = 1, $checked = null, $options = [])
 * @method static bool oldInputIsEmpty()
 * @method static \Illuminate\Support\HtmlString radio($name, $value = null, $checked = null, $options = [])
 * @method static \Illuminate\Support\HtmlString reset($value, $attributes = [])
 * @method static \Illuminate\Support\HtmlString image($url, $name = null, $attributes = [])
 * @method static \Illuminate\Support\HtmlString month($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString color($name, $value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString submit($value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString button($value = null, $options = [])
 * @method static \Illuminate\Support\HtmlString datalist($id, $list = [])
 * @method static void considerRequest($consider = true)
 * @method static \Illuminate\Contracts\Session\Session getSessionStore()
 * @method static static setSessionStore(\Illuminate\Contracts\Session\Session $session)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 * @method static void component($name, $view, array $signature)
 * @method static mixed componentCall($method, $parameters)
 * @method static bool hasComponent(string $name)
 *
 * @see \Kris\LaravelFormBuilder\Supports\FormBuilder
 */
class Form extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'form';
    }
}
