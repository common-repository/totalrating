CREATE TABLE IF NOT EXISTS `{{prefix}}totalrating_widgets`
(
    `id`          int(10) UNSIGNED                                              NOT NULL AUTO_INCREMENT,
    `uid`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `user_id`     int(10) UNSIGNED                                                       DEFAULT NULL,
    `name`        varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `title`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci          DEFAULT NULL,
    `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    `attributes`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci         NOT NULL,
    `created_at`  datetime                                                      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  datetime                                                               DEFAULT NULL,
    `deleted_at`  datetime                                                               DEFAULT NULL,
    `status`      varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL DEFAULT 'open',
    `settings`    text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci         NOT NULL,
    `enabled`     tinyint(4)                                                    NOT NULL DEFAULT '1',
    PRIMARY KEY `id` (`id`),
    KEY `uid` (`uid`),
    KEY `user_id` (`user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
