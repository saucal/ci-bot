#!/bin/bash

#
# Before updating these version numbers and
# hashes, please read the documentation here:
# https://github.com/Automattic/vip-go-ci/#updating-tools-initsh-with-new-versions
#

# https://github.com/squizlabs/PHP_CodeSniffer/releases
export PHP_CODESNIFFER_VER="3.6.2"
export PHP_CODESNIFFER_SHA1SUM="e3fe7a4251223db0bff2ec66974346777129952f"

# https://github.com/WordPress/WordPress-Coding-Standards/releases
export WP_CODING_STANDARDS_VER="2.3.0"
export WP_CODING_STANDARDS_SHA1SUM="c8161d77fcf63bdeaa3e8e6aa36bc1936b469070";

# https://github.com/automattic/vip-coding-standards/releases
export VIP_CODING_STANDARDS_VER="2.3.3"
export VIP_CODING_STANDARDS_SHA1SUM="44c6519c628d450be5330b2706ae9dbb09dbd6be";

# https://github.com/sirbrillig/phpcs-variable-analysis/releases
export PHPCS_VARIABLE_ANALYSIS_VER="2.11.3"
export PHPCS_VARIABLE_ANALYSIS_SHA1SUM="4468db94e39598e616c113a658a69a6265da05d2"

# https://github.com/phpcompatibility/phpcompatibility/releases
export PHP_COMPATIBILITY_VER="9.3.5"
export PHP_COMPATIBILITY_SHA1SUM="880d017ff6c3b64fda2c569bc79e589cc405e9b8";

# https://github.com/phpcompatibility/phpcompatibilitywp/releases
export PHP_COMPATIBILITY_WP_VER="2.1.3"
export PHP_COMPATIBILITY_WP_SHA1SUM="671eb42d7a20008e9259755a72c28c94c0d04e9f"

# https://github.com/phpcompatibility/phpcompatibilityparagonie/releases
export PHP_COMPATIBILITY_PARAGONIE_VER="1.3.1"
export PHP_COMPATIBILITY_PARAGONIE_SHA1SUM="a51cf3a1af05e6192ce9db6fc90ccb7afd58cb22"

# https://github.com/Automattic/vip-go-svg-sanitizer/releases
export VIP_GO_SVG_SANITIZER_VER="0.9.8"
export VIP_GO_SVG_SANITIZER_SHA1SUM="558f16dcff6adc4637c1d0287cc6f95fe9ab2ece"

export TMP_LOCK_FILE="$HOME/.vip-go-ci-tools-init.lck"

function sha1sum_check() {
	FILENAME=$1
	CORRECT_HASH=$2

	TMP_HASH=`sha1sum $FILENAME|awk '{print $1}'`

	if [ "$TMP_HASH" != "$CORRECT_HASH" ] ; then
		echo "FAILED sha1sum check for $FILENAME; $TMP_HASH (downloaded) vs. $CORRECT_HASH (correct)";
		exit;
	fi
}

function lock_place() {
	# Get lock, if that fails, just exit
	if [ -f "$TMP_LOCK_FILE" ] ; then
		echo "$0: Lock in place already, not doing anything."
		exit 0
	fi

	# Acquire lock
	touch "$TMP_LOCK_FILE"
}

function lock_remove() {
	rm -f "$TMP_LOCK_FILE"
}

lock_place


#
# Exit if running as root
#
if [ "$USERNAME" == "root" ] ; then
	echo "$0: Will not run as root, exiting"
	lock_remove
	exit 1

fi


if [ -d ~/vip-go-ci-tools ] ; then
	#
	# We have got the tools installed already,
	# only check in 33% of cases if we should
	# upgrade.
	#
	export TMP_RAND=`seq 1 3 | sort -R | head -n 1`

	if [ "$TMP_RAND" -ne "1" ] ; then
		echo "$0: Will not check for updates at this time, exiting"
		lock_remove
		exit 1
	fi
fi


# Fetch the latest release tag of vip-go-ci
export VIP_GO_CI_VER=""

if [ -f ~/vip-go-ci-tools/vip-go-ci/latest-release.php ] ||
	[ -x ~/vip-go-ci-tools/vip-go-ci/latest-release.php ] ; then
	export VIP_GO_CI_VER=`php ~/vip-go-ci-tools/vip-go-ci/latest-release.php`
fi

if [ "$VIP_GO_CI_VER" == "" ] ; then
	# latest-release.php is not available, fetch it
	# and then fetch the latest release number of vip-go-ci
	TMP_FILE=`mktemp /tmp/vip-go-ci-latest-release-XXXXX.php`

	echo "$0: Trying to determine latest release of vip-go-ci, need to fetch latest-release.php first..."
	wget -O "$TMP_FILE" https://raw.githubusercontent.com/Automattic/vip-go-ci/latest/latest-release.php && \
	chmod u+x "$TMP_FILE" && \
	export VIP_GO_CI_VER=`php $TMP_FILE` && \
	rm "$TMP_FILE" && \
	echo "$0: Latest release of vip-go-ci is: $VIP_GO_CI_VER"
fi

# The release number is not available at all, abort
if [ "$VIP_GO_CI_VER" == "" ] ; then
	echo "$0: Could not determine latest release of vip-go-ci -- aborting";
	lock_remove
	exit 1
fi



if [ -d ~/vip-go-ci-tools ] ; then
	# Tools installed, check if versions installed match with
	# the versions specified in the current version of this file.
	# If not, remove what is already installed and re-install

	# Assume that no re-install is needed
	export TMP_DO_DELETE="0"


	for TMP_FILE in	"vip-coding-standards-$VIP_CODING_STANDARDS_VER.txt" "wp-coding-standards-$WP_CODING_STANDARDS_VER.txt" "php-codesniffer-$PHP_CODESNIFFER_VER.txt" "vip-go-ci-$VIP_GO_CI_VER.txt" "phpcs-variable-analysis-$PHPCS_VARIABLE_ANALYSIS_VER.txt" "php-compatibility-$PHP_COMPATIBILITY_VER.txt" "php-compatibility-wp-$PHP_COMPATIBILITY_WP_VER.txt" "php-compatibility-paragonie-$PHP_COMPATIBILITY_PARAGONIE_VER.txt" "vip-go-svg-sanitizer-$VIP_GO_SVG_SANITIZER_VER.txt" ; do
		if [ ! -f ~/vip-go-ci-tools/$TMP_FILE ] ; then
			export TMP_DO_DELETE="1"
		fi
	done

	if [ "$TMP_DO_DELETE" -eq "1" ] ; then
		echo "$0: Detected obsolete vip-go-ci tools, removing them"
		# One or more of the versions do not match,
		# remove and reinstall
		rm -rf ~/vip-go-ci-tools
		echo "$0: Removed tools"
	fi
fi


if [ -d ~/vip-go-ci-tools ] ; then
	echo "$0: Nothing to update, exiting"
	lock_remove
	exit 0
else

	#
	# No tools installed, do install them,
	#
	echo "$0: No vip-go-ci tools present, will install"

	TMP_FOLDER=`mktemp -d /tmp/vip-go-ci-tools-XXXXXX`

	cd $TMP_FOLDER && \
	wget "https://github.com/squizlabs/PHP_CodeSniffer/archive/$PHP_CODESNIFFER_VER.tar.gz" && \
	sha1sum_check "$PHP_CODESNIFFER_VER.tar.gz" "$PHP_CODESNIFFER_SHA1SUM" && \
	tar -zxvf "$PHP_CODESNIFFER_VER.tar.gz"  && \
	rm -fv "$PHP_CODESNIFFER_VER.tar.gz" && \
	mv "PHP_CodeSniffer-$PHP_CODESNIFFER_VER/" phpcs && \
	touch $TMP_FOLDER/php-codesniffer-$PHP_CODESNIFFER_VER.txt && \
	wget "https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/archive/$WP_CODING_STANDARDS_VER.tar.gz" && \
	sha1sum_check "$WP_CODING_STANDARDS_VER.tar.gz" "$WP_CODING_STANDARDS_SHA1SUM" && \
	tar -zxvf "$WP_CODING_STANDARDS_VER.tar.gz"  && \
	rm -fv "$WP_CODING_STANDARDS_VER.tar.gz" && \
	mv WordPress-Coding-Standards-$WP_CODING_STANDARDS_VER/WordPress* phpcs/src/Standards/ && \
	touch $TMP_FOLDER/wp-coding-standards-$WP_CODING_STANDARDS_VER.txt && \
	wget "https://github.com/Automattic/VIP-Coding-Standards/archive/$VIP_CODING_STANDARDS_VER.tar.gz" && \
	sha1sum_check "$VIP_CODING_STANDARDS_VER.tar.gz" "$VIP_CODING_STANDARDS_SHA1SUM" && \
	tar -zxvf "$VIP_CODING_STANDARDS_VER.tar.gz" && \
	mv "VIP-Coding-Standards-$VIP_CODING_STANDARDS_VER/WordPressVIPMinimum/" phpcs/src/Standards/  && \
	mv "VIP-Coding-Standards-$VIP_CODING_STANDARDS_VER/WordPress-VIP-Go/" phpcs/src/Standards/  && \
	rm -f "$VIP_CODING_STANDARDS_VER".tar.gz && \
	touch $TMP_FOLDER/vip-coding-standards-$VIP_CODING_STANDARDS_VER.txt && \
	wget "https://github.com/sirbrillig/phpcs-variable-analysis/archive/v$PHPCS_VARIABLE_ANALYSIS_VER.tar.gz" && \
	mv "v$PHPCS_VARIABLE_ANALYSIS_VER.tar.gz" "$PHPCS_VARIABLE_ANALYSIS_VER.tar.gz" && \
	sha1sum_check "$PHPCS_VARIABLE_ANALYSIS_VER.tar.gz" "$PHPCS_VARIABLE_ANALYSIS_SHA1SUM" && \
	tar -zxvf "$PHPCS_VARIABLE_ANALYSIS_VER.tar.gz" && \
	mv "phpcs-variable-analysis-$PHPCS_VARIABLE_ANALYSIS_VER/VariableAnalysis/" phpcs/src/Standards/  && \
	rm -f "$PHPCS_VARIABLE_ANALYSIS_VER".tar.gz && \
	touch $TMP_FOLDER/phpcs-variable-analysis-$PHPCS_VARIABLE_ANALYSIS_VER.txt && \
	wget "https://github.com/PHPCompatibility/PHPCompatibility/archive/$PHP_COMPATIBILITY_VER.tar.gz" && \
	sha1sum_check "$PHP_COMPATIBILITY_VER.tar.gz" "$PHP_COMPATIBILITY_SHA1SUM" && \
	tar -zxvf "$PHP_COMPATIBILITY_VER.tar.gz" && \
	mv "PHPCompatibility-$PHP_COMPATIBILITY_VER/PHPCompatibility" phpcs/src/Standards/ && \
	mv "PHPCompatibility-$PHP_COMPATIBILITY_VER/PHPCSAliases.php" phpcs/src/Standards/ && \
	touch "$TMP_FOLDER/php-compatibility-$PHP_COMPATIBILITY_VER.txt" && \
	rm -f "$PHP_COMPATIBILITY_VER.tar.gz" && \
	wget "https://github.com/PHPCompatibility/PHPCompatibilityWP/archive/$PHP_COMPATIBILITY_WP_VER.tar.gz" && \
	sha1sum_check "$PHP_COMPATIBILITY_WP_VER.tar.gz" "$PHP_COMPATIBILITY_WP_SHA1SUM" && \
	tar -zxvf "$PHP_COMPATIBILITY_WP_VER.tar.gz" && \
	mv "PHPCompatibilityWP-$PHP_COMPATIBILITY_WP_VER/PHPCompatibilityWP" phpcs/src/Standards/ && \
	touch $TMP_FOLDER/php-compatibility-wp-$PHP_COMPATIBILITY_WP_VER.txt && \
	rm -f "$PHP_COMPATIBILITY_WP_VER.tar.gz" && \
	wget "https://github.com/PHPCompatibility/PHPCompatibilityParagonie/archive/$PHP_COMPATIBILITY_PARAGONIE_VER.tar.gz" && \
	sha1sum_check "$PHP_COMPATIBILITY_PARAGONIE_VER.tar.gz" "$PHP_COMPATIBILITY_PARAGONIE_SHA1SUM" && \
	tar -zxvf "$PHP_COMPATIBILITY_PARAGONIE_VER.tar.gz" && \
	mv PHPCompatibilityParagonie-$PHP_COMPATIBILITY_PARAGONIE_VER/PHPCompatibilityParagonie* phpcs/src/Standards/ && \
	touch $TMP_FOLDER/php-compatibility-paragonie-$PHP_COMPATIBILITY_PARAGONIE_VER.txt && \
	rm -f "$PHP_COMPATIBILITY_PARAGONIE_VER.tar.gz" && \
	wget "https://github.com/Automattic/vip-go-svg-sanitizer/archive/$VIP_GO_SVG_SANITIZER_VER.tar.gz" && \
	sha1sum_check "$VIP_GO_SVG_SANITIZER_VER.tar.gz" "$VIP_GO_SVG_SANITIZER_SHA1SUM" && \
	tar -zxvf "$VIP_GO_SVG_SANITIZER_VER.tar.gz" && \
	mv "vip-go-svg-sanitizer-$VIP_GO_SVG_SANITIZER_VER" vip-go-svg-sanitizer && \
	touch "$TMP_FOLDER/vip-go-svg-sanitizer-$VIP_GO_SVG_SANITIZER_VER.txt" && \
	rm -f "$VIP_GO_SVG_SANITIZER_VER.tar.gz" && \
	wget "https://github.com/Automattic/vip-go-ci/archive/$VIP_GO_CI_VER.tar.gz" && \
	tar -zxvf "$VIP_GO_CI_VER.tar.gz" && \
	mv "vip-go-ci-$VIP_GO_CI_VER" vip-go-ci && \
	rm -f "$VIP_GO_CI_VER.tar.gz" && \
	touch "$TMP_FOLDER/vip-go-ci-$VIP_GO_CI_VER.txt" && \
	mv $TMP_FOLDER ~/vip-go-ci-tools && \

	# Note that the last action above is atomic:
	# Either moving the folder succeeds, and the tools
	# are all installed, or it fails and no tools are installed.

	echo "$0: Installation of tools finished"
fi

lock_remove
