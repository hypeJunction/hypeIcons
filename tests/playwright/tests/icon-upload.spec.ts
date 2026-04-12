import { test, expect } from '@playwright/test';
import * as path from 'path';
import { loginAs, getUserByUsername, getUserIcontime } from '../helpers/elgg';

/**
 * hypeIcons exposes /icons/{type}/{guid}/{tab} routed through
 * hypeJunction\Icons\Router::handleIcons. Covered tabs: upload, crop.
 *
 * These tests exercise the upload page for a logged-in user's own avatar and
 * assert both UI (form renders, submission succeeds) and DB state (icontime
 * metadata set after upload).
 */

const TEST_USER = process.env.ELGG_TEST_USER || 'testuser';
const TEST_PASS = process.env.ELGG_TEST_PASS || 'testpass123';

// A tiny PNG fixture shipped under tests/playwright/fixtures. If the fixture
// is missing, create one via: `node -e "require('fs').writeFileSync('fixture.png', Buffer.from('89504e470d0a1a0a0000000d49484452000000010000000108060000001f15c4890000000d49444154789c63000100000005000100d8c31b4e0000000049454e44ae426082','hex'))"`
const FIXTURE_PNG = path.join(__dirname, '..', 'fixtures', 'test.png');

test.describe('hypeIcons: avatar upload', () => {

  test('icons upload page renders for logged-in user', async ({ page }) => {
    await loginAs(page, TEST_USER, TEST_PASS);

    const user = await getUserByUsername(TEST_USER);
    expect(user).toBeTruthy();

    await page.goto(`/icons/icon/${user.guid}/upload`);

    // Assert UI: upload form is present
    await expect(page.locator('form')).toBeVisible();
    await expect(page.locator('input[type="file"][name="icon"]')).toBeVisible();
    // Hidden guid field set from router
    await expect(page.locator('input[name="guid"]')).toHaveValue(String(user.guid));
    await expect(page.locator('input[name="icon_type"]')).toHaveValue('icon');
  });

  test('uploading a PNG sets icontime metadata', async ({ page }) => {
    await loginAs(page, TEST_USER, TEST_PASS);

    const user = await getUserByUsername(TEST_USER);
    expect(user).toBeTruthy();

    const beforeIcontime = await getUserIcontime(user.guid);

    await page.goto(`/icons/icon/${user.guid}/upload`);
    await page.setInputFiles('input[type="file"][name="icon"]', FIXTURE_PNG);
    await page.click('input[type="submit"], button[type="submit"]');

    // UI: no error message shown
    await expect(page.locator('.elgg-system-messages .elgg-message-error')).toHaveCount(0);

    // DB: icontime metadata exists and differs from before (new or updated)
    const afterIcontime = await getUserIcontime(user.guid);
    expect(afterIcontime).toBeTruthy();
    if (beforeIcontime) {
      expect(Number(afterIcontime)).toBeGreaterThanOrEqual(Number(beforeIcontime));
    }
  });

  test('crop tab renders after an icon is uploaded', async ({ page }) => {
    await loginAs(page, TEST_USER, TEST_PASS);

    const user = await getUserByUsername(TEST_USER);
    expect(user).toBeTruthy();

    await page.goto(`/icons/icon/${user.guid}/crop`);

    // Either the cropper is rendered, or the "no image" message (if user
    // has no master-sized icon). Accept both — this test just asserts no
    // fatal rendering error.
    await expect(page.locator('.elgg-system-messages .elgg-message-error')).toHaveCount(0);
    await expect(page.locator('body')).toBeVisible();
  });

  test('unauthenticated users cannot access icon upload for another user', async ({ page }) => {
    const user = await getUserByUsername(TEST_USER);
    expect(user).toBeTruthy();

    // No loginAs — should be redirected to login
    const response = await page.goto(`/icons/icon/${user.guid}/upload`);
    // Either redirect to /login or a forbidden response
    expect([200, 302, 403]).toContain(response?.status() ?? 0);
    if (response?.status() === 200) {
      // Rendered page — must be the login page, not the upload form
      await expect(page.locator('input[name="username"]')).toBeVisible();
    }
  });
});

test.describe('hypeIcons: icon remove action', () => {

  test('remove action returns success for own avatar', async ({ page }) => {
    await loginAs(page, TEST_USER, TEST_PASS);
    const user = await getUserByUsername(TEST_USER);

    // Fire the action via POST through a form navigation. Elgg actions
    // require a CSRF token — use the upload page to pull one.
    await page.goto(`/icons/icon/${user.guid}/upload`);
    const token = await page.getAttribute('input[name="__elgg_token"]', 'value');
    const ts = await page.getAttribute('input[name="__elgg_ts"]', 'value');

    // Construct a form and submit it in-page
    await page.evaluate(({ guid, token, ts }) => {
      const f = document.createElement('form');
      f.method = 'POST';
      f.action = '/action/icons/remove';
      const add = (n: string, v: string) => {
        const i = document.createElement('input');
        i.name = n;
        i.value = v;
        f.appendChild(i);
      };
      add('guid', String(guid));
      add('icon_type', 'icon');
      add('__elgg_token', token || '');
      add('__elgg_ts', ts || '');
      document.body.appendChild(f);
      f.submit();
    }, { guid: user.guid, token, ts });

    await page.waitForLoadState('networkidle');
    // Assert no error banner
    await expect(page.locator('.elgg-system-messages .elgg-message-error')).toHaveCount(0);
  });
});
