import app from 'flarum/admin/app';

app.initializers.add('gm-fire-passport', function () {
  app.extensionData
    .for('gm-fire-passport')
    .registerSetting({
      label: app.translator.trans('gm-fire-passport.admin.popup.field.app-auth-url'),
      setting: 'gm-fire-passport.app_auth_url',
      type: 'text',
      placeholder: 'https://example.com/oauth/authorize',
    })
    .registerSetting({
      label: app.translator.trans('gm-fire-passport.admin.popup.field.app-token-url'),
      setting: 'gm-fire-passport.app_token_url',
      type: 'text',
      placeholder: 'https://example.com/oauth/token',
    })
    .registerSetting({
      label: app.translator.trans('gm-fire-passport.admin.popup.field.app-user-url'),
      setting: 'gm-fire-passport.app_user_url',
      type: 'text',
      placeholder: 'https://example.com/api/user',
    })
    .registerSetting({
      label: app.translator.trans('gm-fire-passport.admin.popup.field.app-id'),
      setting: 'gm-fire-passport.app_id',
      type: 'text',
      placeholder: '123',
    })
    .registerSetting({
      label: app.translator.trans('gm-fire-passport.admin.popup.field.app-secret'),
      setting: 'gm-fire-passport.app_secret',
      type: 'text',
      placeholder: 'abcdefghijABCDEFGHIJabcdefghijABCDEFGHIJ',
    })
    .registerSetting({
      label: app.translator.trans('gm-fire-passport.admin.popup.field.app-scopes'),
      setting: 'gm-fire-passport.app_oauth_scopes',
      type: 'text',
    })
    .registerSetting({
      label: app.translator.trans('gm-fire-passport.admin.popup.field.button-title'),
      setting: 'gm-fire-passport.button_title',
      type: 'text',
      placeholder: app.translator.trans('gm-fire-passport.admin.popup.field.button-title-placeholder'),
    })
    .registerSetting({
      label: app.translator.trans('gm-fire-passport.admin.popup.field.button-icon'),
      setting: 'gm-fire-passport.button_icon',
      type: 'text',
      placeholder: 'far fa-id-card',
    });
});
