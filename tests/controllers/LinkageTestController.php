<?php
/**
 * LinkageTestController class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Class LinkageTestController
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class LinkageTestController extends CDbTestCase
{
	public $fixtures = array(
		':user',
		':user_profile',
		':post',
		':post_comment',
		':post_tag',
	);

	public function testLink()
	{
		$user = new LinkageTestUser();
		$this->assertTrue($user->save(), 'Save primary model (user).');

		$profile = new LinkageTestProfile();
		$profile->name = 'Lorem Ipsum';
		$user->link('profile', $profile);
		$this->assertEquals('Lorem Ipsum', $user->profile->name, 'Link profile to user.');

		$post = new LinkageTestPost();
		$post->title = 'Lorem Ipsum';
		$user->link('posts', $post);
		$this->assertEquals(1, $post->user->id, 'Link user to post.');

		$comment = new LinkageTestComment();
		$comment->text = 'Lorem ipsum dolores.';
		$post->link('comments', $comment);
		$this->assertEquals('Lorem ipsum dolores.', $post->comments[0]->text, 'Link comment to post.');

		foreach (explode(',', 'lorem,ipsum') as $name) {
			$tag = new LinkageTestTag();
			$tag->name = $name;
			$post->link('tags', $tag);
		}
		$this->assertCount(2, $post->tags, 'Link two tags to post.');

		foreach (explode(',', 'lorem,ipsum,dolor') as $name) {
			$tag = new LinkageTestTag();
			$tag->name = $name;
			$post->link('tagz', $tag);
		}
		$this->assertCount(3, $post->tagz, 'Link tags through.');


		$post = LinkageTestPost::model()
			->with('comments', 'tags', 'user')
			->findByPk(1);

		$this->assertEquals(1, $post->user->id);
		$this->assertEquals('Lorem Ipsum', $post->user->profile->name);
		$this->assertEquals('Lorem ipsum dolores.', $post->comments[0]->text);
		$this->assertCount(5, $post->tags);
	}

	public function testUnlink()
	{
	}
}