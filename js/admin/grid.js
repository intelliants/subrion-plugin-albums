Ext.onReady(function ()
{
    var pageUrl = intelli.config.admin_url + '/albums/';

    if (Ext.get('js-grid-placeholder')) {
        intelli.albums =
            {
                columns: [
                    'selection',
                    {
                        name: 'title', title: _t('title'), width: 2, renderer: function (value)
                    {
                        return (0 == value.length)
                            ? '<i>' + _t('empty') + '</i>'
                            : value;
                    }
                    },
                    {name: 'username', title: _t('member'), width: 170},
                    {name: 'date', title: _t('date'), width: 100, editor: 'date'},
                    'status',
                    {
                        name: 'path', width: 35, renderer: function (value)
                    {
                        var image = value.split('|');
                        return '<a href="' + intelli.config.ia_url + 'uploads/' + image[0] + 'large/' + image[1] + '" rel="ia_lightbox[slider]"><i class="i-eye grid-icon" style="text" title="' + _t(
                                'view') + '"></i></a>';
                    }
                    },
                    'delete'
                ],
                statuses: ['active', 'approval', 'rejected'],
                url: pageUrl
            };

        intelli.albums = new IntelliGrid(intelli.albums, false);
        intelli.albums.toolbar = Ext.create('Ext.Toolbar', {
            items: [
                {
                    emptyText: _t('text'),
                    name: 'text',
                    listeners: intelli.gridHelper.listener.specialKey,
                    xtype: 'textfield'
                }, {
                    displayField: 'title',
                    editable: false,
                    emptyText: _t('status'),
                    id: 'fltStatus',
                    name: 'status',
                    store: intelli.albums.stores.statuses,
                    typeAhead: true,
                    valueField: 'value',
                    xtype: 'combo'
                }, {
                    handler: function ()
                    {
                        intelli.gridHelper.search(intelli.albums);
                    },
                    id: 'fltBtn',
                    text: '<i class="i-search"></i> ' + _t('search')
                }, {
                    handler: function ()
                    {
                        intelli.gridHelper.search(intelli.albums, true);
                    },
                    text: '<i class="i-close"></i> ' + _t('reset')
                }
            ]
        });

        intelli.albums.init();
    }
});