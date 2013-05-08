<?php
/**
 * LinkageTestComment class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Class LinkageTestComment
 *
 * Attributes:
 * @property int $id
 * @property string $text
 * @property int $post_id
 *
 * Core:
 * @method LinkageTestComment with()
 * @method LinkageTestComment find()
 * @method LinkageTestComment findByPk($pk)
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class LinkageTestComment extends CActiveRecord
{
	/**
	 * @param string $className
	 * @return LinkageTestComment
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'post_comment';
	}
}