<?php
namespace modules\opsModule\models;
    
/**
* The modules\opsModule\models\post
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class post
{
    public static function __render_summary(\core\db\models\note $post) {
        $udate = strtotime($post->updated_at);
        $tags = $post->tags;
?>
        <div class="row article">
            <article class="blog-preview">
                <h3 class="text-justify blog-preview-title" ><?php echo $post->note_title ?></h3>
                <ul class="breadcrumb" style='margin: 0;margin-top: 10px;'>
                    <?php if(count($tags)): ?>
                        <?php foreach($tags as $tag): ?>
                            <li>
                                <a href="/tag/<?php echo urldecode($tag->tag_value); ?>/list">
                                    <span class='glyphicon glyphicon-tag small'></span> <?php echo $tag->tag_value; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>tagView.phtml
                            <li><a href='#'>Untagged</a></li>
                    <?php endif; ?>
                </ul>
                <div class="blog-preview-meta">
                    <span class="day"><?php echo date("M d", $udate) ?></span>
                    <span class="year"><?php echo date("Y", $udate) ?></span>
                    <span class="posts-link" style="color: #DDD">
                        <a href="/view/note/<?php echo $post->note_id ?>" target="__blank" title="Access link to the article">
                            <span class="glyphicon glyphicon-link"></span>
                        </a>
                    </span>
                </div>
                <div class="blog-preview-excerpt" style='padding-right: 10px;padding-left: 10px'>
                    <div class='blog-preview-context'>
                        <?php 
                            $summary = $post->note_summary;
                            if(!strlen($summary))
                                $summary = "<p class='unbalanced-note'><span class='glyphicon glyphicon-warning-sign'></span> No summary pattern detected.</p>";
                            echo $summary;
                        ?>
                    </div>
                    <p style="margin-top: 30px;">
                        <a class="btn btn-sm btn-primary view-article" href="/view/note/<?php echo $post->note_id ?>">View article</a>
                    </p>
                </div>
            </article>
        </div>
<?php
    }
}