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
	public function testLink()
	{
		$this->getFixtureManager()->truncateTables();

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

		// Test databases records.
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
		$this->getFixtureManager()->load(array(
				':user',
				':user_profile',
				':post',
				':post_comment',
				':post_tag',
			));

		$user = LinkageTestUser::model()
			->with(array(
					'posts' => array(
						'with' => array('tags', 'comments'),
					)
				))
			->findByPk(1);

		$this->assertNotNull($user);
		$this->assertCount(2, $user->posts);

		$user->unlink('posts', $user->posts[1], true);
		$this->assertCount(1, $user->posts, 'Unlink user post');

		$post = $user->posts[0];

		$post->unlink('comments', $post->comments[1], true);
		$this->assertCount(1, $post->comments, 'Unlink comment');

		$post->unlink('tags', $post->tags[1]);
		$this->assertCount(2, $post->tags, 'Unlink tag');

		$post->unlink('tagz', $post->tagz[1], true);
		$this->assertCount(1, $post->tagz, 'Unlink tag');

		// Test databases records.
		$user = LinkageTestUser::model()
			->with(array(
					'posts' => array(
						'with' => array('tags', 'comments'),
					)
				))
			->findByPk(1);

		$this->assertCount(1, $user->posts, 'Post count');
		$this->assertCount(1, $user->posts[0]->tags, 'Tags count');
		$this->assertCount(1, $user->posts[0]->comments, 'Comments count');
	}
}