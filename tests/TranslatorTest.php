<?php
namespace Bricks\L18n;
require_once('Translator.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Translator Переводчик.
	 */
	private $translator;

	public function setUp(){
    $this->translator = new Translator;
    $this->translator->locale('en_GB');
    $this->translator->register([
      'en_GB' => [
        'test' => 'Test'
      ]
    ]);
  }

  /**
   * Должен устанавливать и возвращать текущую локаль.
   */
  public function testLocale_shouldSetAndGetLocale(){
    $this->translator->locale('ru_RU');
    $this->assertEquals('ru_RU', $this->translator->locale());
  }

  /**
   * Должен переводить текст.
   */
  public function testStr(){
    $this->assertEquals('Test', $this->translator->str('test'));
  }

  /**
   * Должен использовать значение по умолчанию при отсутствии перевода в 
   * хранилище.
   */
  public function testStr_shouldUseDefaultValue(){
    $this->assertEquals('Hello', $this->translator->str('hello', 'Hello'));
  }

  /**
   * Должен использовать паттерн.
   */
  public function testStr_shouldUsePattern(){
    $this->translator->register(['en_GB' => ['%user' => 'User "%s"']]);
    $this->assertEquals('User "test"', $this->translator->str('%user', '', 'test'));
  }

  /**
   * Должен форматировать числовые велечины.
   */
  public function testNum(){
    $this->translator->register([
      'en_GB' => [
        '0' => [0, '', ''],
        '.0' => [2, '.', ''],
      ]
    ]);
    $this->assertEquals('10', $this->translator->num(10));
    $this->assertEquals('10.50', $this->translator->num(10.5));
  }

  /**
   * Должен использовать формат по умолчанию в случае отсутствия формата в 
   * хранилище.
   */
  public function testNum_shouldUseDefaultPattern(){
    $this->assertEquals('10', $this->translator->num(10, [0, '', '']));
    $this->assertEquals('10.50', $this->translator->num(10.5, [2, '.', '']));
  }

  /**
   * Должен форматировать денежные велечины.
   */
  public function testMoney(){
    $this->translator->register([
      'en_GB' => [
        '$' => [2, ',', ' ']
      ]
    ]);
    $this->assertEquals('12 000,50', $this->translator->money(12000.5));
  }

  /**
   * Должен использовать формат по умолчанию в случае отсутствия формата в 
   * хранилище.
   */
  public function testMoney_shouldUseDefaultPattern(){
    $this->assertEquals('12 000,50', $this->translator->money(12000.5, [2, ',', ' ']));
  }
}
