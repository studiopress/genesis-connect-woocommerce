const fs = require("fs");
const path = require("path");
const AdmZip = require("adm-zip");
const { minimatch } = require("minimatch");
const { execSync } = require("child_process");

function getPluginVersion() {
	const pluginFile = fs.readFileSync("genesis-connect-woocommerce.php", "utf8");
	const versionMatch = pluginFile.match(/Version:\s*(.+)/);
	return versionMatch ? versionMatch[1].trim() : "unknown";
}

function getIgnorePatterns() {
	const distignore = fs.readFileSync(".svnignore", "utf8");
	return distignore
		.split("\n")
		.map((line) => line.trim())
		.filter((line) => line && !line.startsWith("#"))
		.map((pattern) => `*${pattern}*`);
}

function shouldIgnore(filePath, ignorePatterns) {
	const relativePath = path.relative(process.cwd(), filePath);
	return ignorePatterns.some((pattern) =>
		minimatch(relativePath, pattern, { matchBase: true, dot: true })
	);
}

function runBuildSteps() {
	console.log("Running build steps...");

	try {
		console.log("Generating language file...");
		execSync(
            "wp i18n make-pot . languages/genesis-connect-woocommerce.pot --exclude=config,node_modules,scripts,vendor --headers='{ \"Report-Msgid-Bugs-To\": \"StudioPress <translations@studiopress.com>\" }' --exclude=bin/ --quiet",
			{ stdio: "inherit" }
		);
		console.log("Build steps completed successfully.");
	} catch (error) {
		console.error("Error during build steps:", error);
		process.exit(1);
	}
}

function createZip() {
	runBuildSteps();

	const version = getPluginVersion();
	const ignorePatterns = getIgnorePatterns();
	const zipFileName = `genesis-connect-woocommerce.${version}.zip`;

	const zip = new AdmZip();

	function addDirectoryToZip(directory) {
		const files = fs.readdirSync(directory);
		for (const file of files) {
			const filePath = path.join(directory, file);
			const stats = fs.statSync(filePath);
			const relativePath = path.relative(process.cwd(), filePath);

			if (shouldIgnore(filePath, ignorePatterns)) {
				continue;
			}

			if (stats.isDirectory()) {
				addDirectoryToZip(filePath);
			} else {
				zip.addFile(
					path.join("genesis-connect-woocommerce", relativePath),
					fs.readFileSync(filePath)
				);
			}
		}
	}

	addDirectoryToZip(process.cwd());

	zip.writeZip(zipFileName);
	console.log(`Created ${zipFileName}`);
}

createZip();
