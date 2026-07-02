-- ============================================================
-- Deploy Sessão 4 — Menu items
-- Target: dev-preview (roteirot_dev)
-- ============================================================

START TRANSACTION;

-- 1. DELETE old broken menu items (by post_name, not ID)
DELETE FROM rw_posts
WHERE post_type = 'nav_menu_item'
  AND post_name IN ('hoteis-fazenda', 'sana-macae');

-- 2. Clean orphaned postmeta
DELETE pm FROM rw_postmeta pm
LEFT JOIN rw_posts p ON p.ID = pm.post_id
WHERE p.ID IS NULL;

-- 3. Resolve parent: Região dos Lagos menu item
SET @parent_id = (
  SELECT ID FROM rw_posts
  WHERE post_name = 'regiao-dos-lagos' AND post_type = 'nav_menu_item'
  LIMIT 1
);

-- 4. Resolve menu taxonomy ID
SET @menu_tt_id = (
  SELECT tt.term_taxonomy_id FROM rw_terms t
  JOIN rw_term_taxonomy tt ON tt.term_id = t.term_id
  WHERE t.slug = 'menu-principal' LIMIT 1
);

-- 5. Resolve Hotéis Fazenda page ID (may not exist yet — PHP cria depois)
SET @hf_page = (
  SELECT ID FROM rw_posts
  WHERE post_name = 'hoteis-fazenda' AND post_type = 'page'
  LIMIT 1
);

-- ============================================================
-- MACAÉ (RJ)
-- ============================================================
INSERT INTO rw_posts
  (post_author, post_date, post_date_gmt, post_title, post_status,
   post_name, post_modified, post_modified_gmt, post_type, menu_order)
VALUES
   (1, NOW(), UTC_TIMESTAMP(), 'Macaé (RJ)', 'publish',
   'macae-rj', NOW(), UTC_TIMESTAMP(), 'nav_menu_item', 999);

SET @m = LAST_INSERT_ID();

INSERT INTO rw_postmeta (post_id, meta_key, meta_value) VALUES
  (@m, '_menu_item_type', 'post_type'),
  (@m, '_menu_item_object_id', '56'),
  (@m, '_menu_item_object', 'page'),
  (@m, '_menu_item_menu_item_parent', IFNULL(CAST(@parent_id AS CHAR), '0')),
  (@m, '_menu_item_target', ''),
  (@m, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
  (@m, '_menu_item_xfn', ''),
  (@m, '_menu_item_url', '');

INSERT IGNORE INTO rw_term_relationships (object_id, term_taxonomy_id)
VALUES (@m, @menu_tt_id);

-- ============================================================
-- SANA (RJ)
-- ============================================================
INSERT INTO rw_posts
  (post_author, post_date, post_date_gmt, post_title, post_status,
   post_name, post_modified, post_modified_gmt, post_type, menu_order)
VALUES
  (1, NOW(), UTC_TIMESTAMP(), 'Sana (RJ)', 'publish',
   'sana-rj', NOW(), UTC_TIMESTAMP(), 'nav_menu_item', 999);

SET @s = LAST_INSERT_ID();

INSERT INTO rw_postmeta (post_id, meta_key, meta_value) VALUES
  (@s, '_menu_item_type', 'post_type'),
  (@s, '_menu_item_object_id', '68'),
  (@s, '_menu_item_object', 'page'),
  (@s, '_menu_item_menu_item_parent', IFNULL(CAST(@parent_id AS CHAR), '0')),
  (@s, '_menu_item_target', ''),
  (@s, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
  (@s, '_menu_item_xfn', ''),
  (@s, '_menu_item_url', '');

INSERT IGNORE INTO rw_term_relationships (object_id, term_taxonomy_id)
VALUES (@s, @menu_tt_id);

-- ============================================================
-- HOTÉIS FAZENDA (top-level, aponta para page)
-- ============================================================
INSERT INTO rw_posts
  (post_author, post_date, post_date_gmt, post_title, post_status,
   post_name, post_modified, post_modified_gmt, post_type, menu_order)
VALUES
  (1, NOW(), UTC_TIMESTAMP(), 'Hotéis Fazenda', 'publish',
   'hoteis-fazenda', NOW(), UTC_TIMESTAMP(), 'nav_menu_item', 998);

SET @h = LAST_INSERT_ID();

INSERT INTO rw_postmeta (post_id, meta_key, meta_value) VALUES
  (@h, '_menu_item_type', 'post_type'),
  (@h, '_menu_item_object_id', IFNULL(CAST(@hf_page AS CHAR), '0')),
  (@h, '_menu_item_object', 'page'),
  (@h, '_menu_item_menu_item_parent', '0'),
  (@h, '_menu_item_target', ''),
  (@h, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
  (@h, '_menu_item_xfn', ''),
  (@h, '_menu_item_url', '');

INSERT IGNORE INTO rw_term_relationships (object_id, term_taxonomy_id)
VALUES (@h, @menu_tt_id);

COMMIT;
