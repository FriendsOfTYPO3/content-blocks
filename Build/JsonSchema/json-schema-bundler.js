import $RefParser from "@apidevtools/json-schema-ref-parser";
import { writeFile, mkdir } from "node:fs/promises";
import { dirname, join } from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = dirname(fileURLToPath(import.meta.url));

let schema = await $RefParser.bundle(join(__dirname, 'SchemaSources/content-element.json'));
let jsonString = JSON.stringify(schema, null, 2);

const outputPath = join(__dirname, '../../Resources/Private/JsonSchema/content-element.schema.json');
await mkdir(dirname(outputPath), { recursive: true });
await writeFile(outputPath, jsonString);
