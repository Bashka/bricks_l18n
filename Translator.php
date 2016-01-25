<?php
namespace Bricks\L18n;

/**
 * Средство интернационализации приложения.
 *
 * @author Artur Sh. Mamedbekov
 */
class Translator{
  /**
   * @var array Хранилище переводов, используемое для интернационализации.
   * Структура: [локаль => [ключ => значение], ...].
   */
  private $translation;

  /**
   * @var string Используемая локаль вида язык_СТРАНА (на пример ru_RU).
   */
  private $locale;

  /**
   * Получение значения перевода для текущей локали по ключу.
   *
   * @param string $key Ключ целевого перевода.
   * @param string $default Перевод по умолчанию, который будет возвращен в 
   * случае отсутствия целевого перевода.
   *
   * @return string Запрашиваемый перевод.
   */
  protected function get($key, $default){
    if(!isset($this->translation[$this->locale()])){
      return $default;
    }

    if(!isset($this->translation[$this->locale()][$key])){
      return $default;
    }

    return $this->translation[$this->locale()][$key];
  }

  public function __construct(){
      $this->translation = [];
      list($this->locale) = explode('.', setlocale(LC_CTYPE, '0'));
  }

  /**
   * Регистрирует дополнительное хранилище переводов, сливая его с имеющимся. В 
   * случае конфликта, значение заменяется на регистрируемое.
   *
   * @param array $translation Регистрируемое хранилище переводов.
   * Структура: [локаль => [ключ => значение]].
   */
  public function register(array $translation){
    $this->translation = array_replace_recursive($this->translation, $translation);
  }

  /**
   * Устанавливает или получает текущую локаль.
   *
   * @param string $locale [optional] Устанавливаемая локаль.
   *
   * @return string Текущая локаль. Значение будет возвращено только если методу 
   * не передан параметр.
   */
  public function locale($locale = null){
    if(is_null($locale)){
      return $this->locale;
    }

    $this->locale = $locale;
  }

  /**
   * Предоставляет перевод для данного ключа.
   *
   * @param string $key Ключ целевого перевода. Если ключ начинается со знака 
   * %, перевод будет использоваться как шаблон для функции sprintf.
   * @param string $default [optional] Перевод по умолчанию, используемый в 
   * случае отсутствия целевого перевода.
   * @param mixed ... [optional] Параметры, подставляемые в шаблон перевода.  
   * Эти параметры актуальны только при использовании ключа, начинающегося со 
   * знака %.
   *
   * @return string Перевод.
   */
  public function str($key, $default = ''){
    $value = $this->get($key, $default);
    if($key[0] != '%'){
      return $value;
    }

    $args = func_get_args();
    array_splice($args, 0, 2, [$value]);

    return call_user_func_array('sprintf' , $args);
  }

  /**
   * Форматирует число в соответствии с текущей локалью.
   *
   * @param int|float $number Форматируемое число.
   * @param array $default Формат числа по умолчанию. Данное значение 
   * используется при отсутствии в хранилище для текущей локали ключа "0" или 
   * ".0" (в зависимости от типа первого параметра).
   * Структура: [числоДробныхРазрядов, разделительДробнойЧасти, 
   * разделительТысяч].
   *
   * @return string Отформатированное значение.
   */
  public function num($number, $default = [2, '.', '']){
    $args = $this->get(is_int($number)? '0' : '.0', $default);
    array_unshift($args, $number);

    return call_user_func_array('number_format', $args);
  }

  /**
   * Форматирует денежную велечину в соответствии с текущей локалью.
   *
   * @param int|float $number Форматируемое значение.
   * @param array $default Формат денежной велечины по умолчанию. Данное 
   * значение используется при отсутствии в хранилище для текущей локали ключа 
   * "$". Структура: [числоДробныхРазрядов, разделительДробнойЧасти, 
   * разделительТысяч].
   *
   * @return string Отформатированное значение.
   */
  public function money($number, $default = [2, '.', ' ']){
    $args = $this->get('$', $default);
    array_unshift($args, $number);

    return call_user_func_array('number_format', $args);
  }
}
