import $RefParser from "@apidevtools/json-schema-ref-parser";
import { writeFile, mkdir } from "node:fs/promises";
import { dirname, join } from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = dirname(fileURLToPath(import.meta.url));

let contentElementSchema = await $RefParser.bundle(join(__dirname, 'SchemaSources/content-element.json'));
let contentElementSchemaJson = JSON.stringify(contentElementSchema, null, 2);

let pageTypeSchema = await $RefParser.bundle(join(__dirname, 'SchemaSources/page-type.json'));
let pageTypeSchemaJson = JSON.stringify(pageTypeSchema, null, 2);

let recordTypeSchema = await $RefParser.bundle(join(__dirname, 'SchemaSources/record-type.json'));
let recordTypeSchemaJson = JSON.stringify(recordTypeSchema, null, 2);

let fileTypeSchema = await $RefParser.bundle(join(__dirname, 'SchemaSources/file-type.json'));
let fileTypeSchemaJson = JSON.stringify(fileTypeSchema, null, 2);

let basicSchema = await $RefParser.bundle(join(__dirname, 'SchemaSources/basic.json'));
let basicSchemaJson = JSON.stringify(basicSchema, null, 2);

const contentElementOutputPath = join(__dirname, '../../JsonSchema/content-element.schema.json');
await mkdir(dirname(contentElementOutputPath), { recursive: true });
await writeFile(contentElementOutputPath, contentElementSchemaJson);

const pageTypeOutputPath = join(__dirname, '../../JsonSchema/page-type.schema.json');
await mkdir(dirname(pageTypeOutputPath), { recursive: true });
await writeFile(pageTypeOutputPath, pageTypeSchemaJson);

const recordTypeOutputPath = join(__dirname, '../../JsonSchema/record-type.schema.json');
await mkdir(dirname(recordTypeOutputPath), { recursive: true });
await writeFile(recordTypeOutputPath, recordTypeSchemaJson);

const basicOutputPath = join(__dirname, '../../JsonSchema/basic.schema.json');
await mkdir(dirname(basicOutputPath), { recursive: true });
await writeFile(basicOutputPath, basicSchemaJson);

const fileTypeOutputPath = join(__dirname, '../../JsonSchema/file-type.schema.json');
await mkdir(dirname(fileTypeOutputPath), { recursive: true });
await writeFile(fileTypeOutputPath, fileTypeSchemaJson);
