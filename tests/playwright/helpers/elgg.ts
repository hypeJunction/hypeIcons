import { Page } from '@playwright/test';
import mysql from 'mysql2/promise';

const DB_CONFIG = {
  host: process.env.ELGG_DB_HOST || 'db',
  port: Number(process.env.ELGG_DB_PORT || 3306),
  user: process.env.ELGG_DB_USER || 'elgg',
  password: process.env.ELGG_DB_PASS || 'elgg',
  database: process.env.ELGG_DB_NAME || 'elgg',
};

export async function loginAs(page: Page, username: string, password: string = 'testpass123') {
  await page.goto('/login');
  await page.fill('input[name="username"]', username);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"]');
  await page.waitForURL(/\//);
}

export async function queryDb(sql: string, params: any[] = []) {
  const conn = await mysql.createConnection(DB_CONFIG);
  const [rows] = await conn.execute(sql, params);
  await conn.end();
  return rows as any[];
}

export async function getUserByUsername(username: string) {
  const rows = await queryDb(
    'SELECT e.guid, e.type, e.subtype, u.username, u.name FROM elgg_entities e JOIN elgg_users_entity u ON u.guid = e.guid WHERE u.username = ? LIMIT 1',
    [username]
  );
  return rows[0];
}

export async function getUserIcontime(guid: number) {
  const rows = await queryDb(
    "SELECT value FROM elgg_metadata WHERE entity_guid = ? AND name = 'icontime' LIMIT 1",
    [guid]
  );
  return rows[0]?.value ?? null;
}
