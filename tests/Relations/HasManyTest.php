<?php

namespace Imanghafoori\EloquentMockery\Tests;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\EloquentMockery\MockableModel;
use PHPUnit\Framework\TestCase;

class HasManyUser extends Model
{
    use MockableModel;

    public function comments()
    {
        return $this->hasMany(HasManyComment::class, 'user_id');
    }
}

class HasManyComment extends Model
{
    use MockableModel;

    public function user()
    {
        return $this->belongsTo(HasManyUser::class);
    }
}

class HasManyTest extends TestCase
{
    /**
     * @test
     */
    public function has_many()
    {
        HasManyUser::addFakeRow(['id' => 1, 'name' => 'Iman 1']);
        HasManyUser::addFakeRow(['id' => 2, 'name' => 'Iman 2']);
        HasManyUser::addFakeRow(['id' => 3, 'name' => 'Iman 3']);
        HasManyUser::addFakeRow(['id' => 4, 'name' => 'Iman 4']);

        HasManyComment::addFakeRow(['id' => 1, 'user_id' => 1,'comment' => 'sss']);
        HasManyComment::addFakeRow(['id' => 2, 'user_id' => 1,'comment' => 'aaa']);
        HasManyComment::addFakeRow(['id' => 3, 'user_id' => 2,'comment' => 'bbb']);
        HasManyComment::addFakeRow(['id' => 4, 'user_id' => 2,'comment' => 'ccc']);
        HasManyComment::addFakeRow(['id' => 5, 'user_id' => 3, 'comment' => 'ddd']);

        $this->assertEquals(2, HasManyUser::find(1)->comments()->count());
        $this->assertEquals(1, HasManyUser::find(1)->comments()->where('comment', 'aaa')->count());
        $this->assertEquals(1, HasManyUser::find(1)->comments()->where('comment', 'aaa')->get()->count());
        $this->assertEquals(2, HasManyUser::find(2)->comments()->count());
        $this->assertEquals(1, HasManyUser::find(3)->comments()->count());
        $this->assertEquals(0, HasManyUser::find(4)->comments()->count());

        $this->assertEquals(2, HasManyUser::find(1)->comments->count());
        $this->assertEquals(2, HasManyUser::find(2)->comments->count());
        $this->assertEquals(1, HasManyUser::find(3)->comments->count());
        $this->assertEquals(0, HasManyUser::find(4)->comments->count());

        $comments = HasManyUser::find(3)->comments;
        $this->assertEquals('ddd', $comments[0]->comment);

        $comments = HasManyUser::find(1)->comments;
        $this->assertEquals('sss', $comments[0]->comment);

        $this->assertEquals('aaa', HasManyUser::find(1)->comments()->where('comment', 'aaa')->first()->comment);

        $this->assertEquals(1, HasManyComment::query()->find(1)->user->id);
        $this->assertEquals(1, HasManyComment::query()->find(1)->user()->count());

        $this->assertEquals(1, HasManyComment::query()->find(2)->user->id);
        $this->assertEquals(1, HasManyComment::query()->find(2)->user()->count());

        $this->assertEquals(2, HasManyComment::query()->find(3)->user->id);
        $this->assertEquals(1, HasManyComment::query()->find(3)->user()->count());

        $f = HasManyComment::query()->find(3)->user()->create([
            'name' => 'created'
        ]);

        $this->assertNotNull($f->created_at);
        $this->assertNotNull($f->updated_at);
        $this->assertNotNull(HasManyUser::find(5));
        $this->assertEquals(5, $f->id);
        $this->assertEquals('created', $f->name);

        $this->assertEquals('created', $f->name);
        $this->assertEquals(5, HasManyUser::count());


        $comment = HasManyUser::find(4)->comments()->create([
            'comment' => 'created!'
        ]);
        $this->assertEquals('created!', $comment->comment);
        $this->assertEquals(6, $comment->id);
        $this->assertEquals(4, $comment->user_id);
        $this->assertNotNull($comment->created_at);
        $this->assertNotNull($comment->updated_at);
    }
}
