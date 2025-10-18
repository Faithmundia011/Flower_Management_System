-- Database Backup for: flower_mng
-- Generated on: 2025-10-18 17:38:00

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `price`) STORED,
  `payment_status` enum('Unpaid','Paid') DEFAULT 'Unpaid',
  `status` enum('Pending','Processing','Delivered','Cancelled') DEFAULT 'Pending',
  `order_date` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_order_user` (`user_id`),
  KEY `fk_order_products` (`products_id`),
  CONSTRAINT `fk_order_products` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`id`, `user_id`, `products_id`, `quantity`, `price`, `total_price`, `payment_status`, `status`, `order_date`) VALUES ('11', '14', '1', '1', '0.00', '0.00', 'Paid', '', '2025-10-15 17:03:15');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `price`, `stock`, `image`, `created_at`, `is_active`) VALUES ('1', 'Rose flower', '1000.00', '490', '1759730159_Rose flower.webp', '2025-09-29 12:09:51', '1');
INSERT INTO `products` (`id`, `name`, `price`, `stock`, `image`, `created_at`, `is_active`) VALUES ('2', 'sunflower', '1500.00', '39', '1759225497_sunflower.webp', '2025-09-29 17:04:07', '1');
INSERT INTO `products` (`id`, `name`, `price`, `stock`, `image`, `created_at`, `is_active`) VALUES ('3', 'Red-rose flower', '1500.00', '56', '1759225481_red-rose-flower.jpg', '2025-09-30 11:41:55', '1');
INSERT INTO `products` (`id`, `name`, `price`, `stock`, `image`, `created_at`, `is_active`) VALUES ('4', 'palm red bouquet', '1200.00', '98', '1759225461_palm red bouquet.png', '2025-09-30 12:44:21', '1');
INSERT INTO `products` (`id`, `name`, `price`, `stock`, `image`, `created_at`, `is_active`) VALUES ('5', 'fresh_cut flowers', '3000.00', '8', '1759259694_fres_cut.jpg', '2025-09-30 22:14:54', '1');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `phone`) VALUES ('1', 'Faith', '$2y$10$ExCvleV7Ef./O7zSqoxI/.1.u1H7c1gqlOEPG3jqogXZjz2TP9KVm', 'admin', '2025-09-25 12:01:51', '');
INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `phone`) VALUES ('2', 'jane', '$2y$10$u7UGj6tBklZqCb.rSELPqOZ6hHbxpBu1DpxYSkdg.T0Q.toj3Qaka', 'customer', '2025-09-25 12:17:33', '');
INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `phone`) VALUES ('11', 'davi', '$2y$10$dG5mF9yFrzoe/Lnw/Or9EOAMnvaRGcNFPeuxMPr/H.t9BVFSZ6ozy', 'customer', '2025-10-07 14:13:07', '');
INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `phone`) VALUES ('12', 'benard', '$2y$10$NcY/H15CoXjaDUiH3nwZU.O0f0RzCETfzmBe4C1CsPzxOoTCiAM92', 'customer', '2025-10-13 09:51:20', '');
INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `phone`) VALUES ('13', 'janny', '$2y$10$mkGs45/rsnCd72Ajylja5eWKrGaol9Gauhg.W7b92W1xQdk8GCJDS', 'customer', '2025-10-13 09:52:24', '');
INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `phone`) VALUES ('14', 'fei', '$2y$10$spAWEhKspVbNRdkGJSNb.ub2UbTVOYa4RL2ppiHIaMmHaDMwj9jWS', 'customer', '2025-10-14 22:38:04', '');

SET FOREIGN_KEY_CHECKS=1;
