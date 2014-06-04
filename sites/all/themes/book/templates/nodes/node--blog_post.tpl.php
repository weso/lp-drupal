<?php

/**
 * @file
 * Bartik's theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 */
?>
<?php
  require_once(drupal_get_path("theme", "book") ."/template-loader.php");
  get_template("debate-header", "blog", $application_data, $theme_path);
  $labels = get_labels($application_data['languages']);
  $node_id = $node->nid;
  // Rules for edition and deletion of content
  global $user; //The user viewing the page
  $can_edit = $is_admin;
  $can_delete = $is_admin;
?>
<div class="content main-content container">
  <!-- Breadcrumbs -->
  <ol class="breadcrumb">
    <li><a href="/"><?php echo $labels["index"]; ?></a></li>
    <li><a href="/debate"><?php echo $labels["land_debate"]; ?></a></li>
    <li><a href="/debate/blog"><?php echo $labels["blog"]; ?></a></li>
    <li class="active"><?php echo $title; ?></li>
  </ol>
  <div class="row">
    <div class="col-sm-12">
      <h1>
        <span class="country-name">
          <?php echo $title; ?>
        </span>
      </h1>
    </div>
  </div>
  <div class="row node-view">
    <div class="col-sm-3">
      <div class="image">
        <?php print render($content["field_image"]); ?>
      </div>
      <!-- Share buttons -->
      <div class="social-buttons">
        <h2 class="section">
          <span><?php echo $labels["share"]; ?></span>
        </h2>
        <script src="<?php echo "{$theme_path}/js/share.js"; ?>"></script>
      </div>
      <!-- Edition buttons -->
      <div class="row edition-buttons">
        <?php if ($can_edit): ?>
          <a href="<?php echo "/node/$node_id/edit"; ?>"><button class="btn data-button"><?php echo $labels['edit']; ?></button></a>
        <?php endif; ?>
        <?php if ($can_delete): ?>
          <a href="<?php echo "/node/$node_id/delete"; ?>"><button class="btn data-button"><?php echo $labels['delete']; ?></button></a>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-sm-9">
      <header class="entry-header">
        <!-- Related topics -->
        <div class="topics">
          <?php print render($content["field_related_topics"]); ?>
        </div>
        <!-- Author and date -->
        <div class="user date">
          <?php echo $labels["written_by"]; ?>
          <?php echo render($name); ?>
          <?php echo $labels["on"], " ", date($labels["date_format"], $created); ?>
        </div>
      </header>
      <!-- Body -->
      <div class="body">
        <?php print render($content["body"]); ?>
      </div>
      <!-- Comments -->
      <div class="entry-comments">
        <h2 class="section">
          <?php if ($comment_count > 0): ?>
            <span><?php echo $labels["user_comments"]; ?></span>
          <?php else: ?>
            <span><?php echo "CURRENTLY THERE ARE NO COMMENTS"; ?></span>
          <?php endif; ?>
        </h2>
        <?php
          // Remove the "Add new comment" link on the teaser page or if the comment
          // form is being displayed on the same page.
          if ($teaser || !empty($content['comments']['comment_form'])) {
            unset($content['links']['comment']['#links']['comment-add']);
          }
        ?>
        <?php print render($content['comments']); ?>
      </div>
    </div>
  </div>
</div>
<?php get_template("footer", "events", $application_data, $theme_path); ?>