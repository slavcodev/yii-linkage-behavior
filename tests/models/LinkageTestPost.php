<?php
/**
 * LinkageTestPost class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Class LinkageTestPost
 *
 * Attributes:
 * @property int $id
 * @property string $title
 * @property int $user_id
 *
 * Relations:
 * @property LinkageTestPostTag[] $tags
 * @property LinkageTestComment[] $comments
 * @property LinkageTestUser $user
 *
 * Behaviors:
 * @property Linkage $linkage
 * @method LinkageTestPost link($name, $model, array $extraColumns = array())
 * @method LinkageTestPost unlink($name, $model, $delete = false)
 *
 * Core:
 * @method LinkageTestPost with()
 * @method LinkageTestPost find()
 * @method LinkageTestPost findByPk($pk)
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class LinkageTestPost extends CActiveRecord
{
	/**
	 * @param string $className
	 * @return LinkageTestPost
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'post';
	}

	public function behaviors()
	{
		return array(
			'linkage' => array(
				'class' => 'Linkage',
			),
		);
	}

	public function rules()
	{
		return array(
			array('title', 'required'),
		);
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'LinkageTestUser', 'user_id'),
			'comments' => array(self::HAS_MANY, 'LinkageTestComment', 'post_id'),
			'tags' => array(self::MANY_MANY, 'LinkageTestTag', 'post_tag(post_id, tag_id)'),

			'postTag' => array(self::HAS_MANY, 'LinkageTestPostTag', 'post_id'),
			'tagz' => array(self::HAS_MANY, 'LinkageTestTag', 'tag_id', 'through' => 'postTag'),
		);
	}
}

/**
 * Class LinkageTestPostTag
 *
 * Attributes:
 * @property int $post_id
 * @property int $tag_id
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class LinkageTestPostTag extends CActiveRecord
{
	/**
	 * @param string $className
	 * @return LinkageTestPostTag
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'post_tag';
	}

	public function primaryKey()
	{
		return array('post_id', 'tag_id');
	}
}