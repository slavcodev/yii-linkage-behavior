# Linkage AR behavior



## Usage

```php
$post = Post::model()->findByPk(1);

// BELONGS_TO relations cannot link, use relations in owner models.
$user = User::model()->findByPk(Yii::app()->user->id);
$user->link('posts', $post);

// HAS_ONE relations
$profile = new Profile();
$profile->name = 'Yii Framework';
$user->link('profile', $profile);

// HAS_MANY relations
$comment = new Comment();
$comment->text = 'Lorem ipsum.';
$post->link('comments', $comment);

// MANY_MANY relations
foreach (preg_split('#\s+,\s+#', 'lorem, ipsum') as $name) {
	$tag = new Tag();
	$tag->name = $name;
	$post->link('tags', $tag);
}
```