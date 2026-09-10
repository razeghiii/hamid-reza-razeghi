APP_NAME := negar-ads
PACKAGE := $(APP_NAME)-final.zip
FILES := README.md .gitignore config.example.php index.php install.php click.php impression.php database assets

.PHONY: package clean-package

package:
	rm -f $(PACKAGE)
	zip -rq $(PACKAGE) $(FILES) -x '*/.DS_Store'
	@echo "فایل نهایی ایجاد شد: $(PACKAGE)"

clean-package:
	rm -f $(PACKAGE)
