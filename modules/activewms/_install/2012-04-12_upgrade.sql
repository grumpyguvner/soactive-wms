ALTER TABLE `styles_images`
  DROP INDEX `styleid`;

ALTER TABLE `styles_images`
  ADD INDEX `styles_images_styleid_colourid` (`styleid`, `colourid`, `displayorder`);
