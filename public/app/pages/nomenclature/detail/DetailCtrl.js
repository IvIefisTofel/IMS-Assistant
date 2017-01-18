/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
    'use strict';

    angular.module('BlurAdmin.pages.nomenclature')
        .controller('DetailCtrl', DetailCtrl);

    /** @ngInject */
    function DetailCtrl($scope, $stateParams, $http, $window, $uibModal) {
        var cropper,
            currImgId = null,
            imgDef = {
                width: undefined,
                height: undefined
            };

        function getAspect(img) {
            if (img.width > img.height) {
                return 29.7 / 21.0;
            } else {
                return 21.0 / 29.7;
            }
        }

        function isImageFile(file) {
            if (file.type) {
                return /^image\/\w+$/.test(file.type);
            } else {
                return /\.(jpg|jpeg|png|gif)$/.test(file);
            }
        }

        function isEmpty(obj) {
            if (obj == null) return true;
            if (obj.length > 0)    return false;
            if (obj.length === 0)  return true;
            if (typeof obj !== "object") return true;
            for (var key in obj) {
                if (Object.prototype.hasOwnProperty.call(obj, key)) return false;
            }

            return true;
        }

        function getCropDate(date) {
            if (date !== null && date instanceof Date) {
                return new Date(date.getFullYear(), date.getMonth(), date.getDate());
            } else if (date !== null && typeof(date) == 'number') {
                date = new Date(date);
                return new Date(date.getFullYear(), date.getMonth(), date.getDate());
            } else {
                return null
            }
        }

        function clearValues(){
            $scope.new.patterns = [];
            $scope.galleries.new = [];
            $scope.new.add = {
                patterns: [],
                models: [],
                projects: []
            };
            $scope.new.update = {
                patterns: {},
                models: {},
                projects: {}
            };
            $('input[type="file"][name^="file_id_"],#add-model,#add-project').val(null);
        }

        $scope.actionVariants = {
            view: [
                {
                    text: "Редактировать",
                    class: "btn-primary",
                    iconClass: "fa fa-pencil",
                    action: 'switchEdit'
                },
                {
                    text: "Обновить",
                    class: "btn-info",
                    iconClass: "fa fa-refresh",
                    action: 'refreshData'
                }
            ],
            edit: [
                {
                    text: "Сохранить",
                    class: "btn-success",
                    iconClass: "fa fa-floppy-o",
                    action: 'saveData'
                },
                {
                    text: "Отменить",
                    class: "btn-danger",
                    iconClass: "fa fa-ban",
                    action: 'switchEdit'
                },
                {
                    text: "Обновить",
                    class: "btn-info",
                    iconClass: "fa fa-refresh",
                    action: 'refreshData'
                }
            ]
        };
        $scope.actions = $scope.actionVariants.view;

        $scope.edit = false;
        $scope.preview = {
            detail: null,
            selected: {
                client: null,
                order: null
            }
        };

        $scope.detail = {};
        $scope.orders = [];
        $scope.groups = [];
        $scope.loading = true;
        $scope.galleries = {
            main: [],
            new: []
        };
        $scope.gallery = {
            images: null,
            methods: {}
        };
        $scope.openGallery = function (id, index) {
            if (index == undefined) {
                index = 0;
            }
            if ($scope.galleries[id] != undefined) {
                $scope.gallery.images = $scope.galleries[id];
                $scope.gallery.methods.open(index);
            }
        };

        $scope.new = {
            patterns: [],
            updPatterns: function () {
                $scope.new.patterns = [];
                $scope.galleries.new = [];
                angular.forEach($scope.new.update.patterns, function (val, key) {
                    $scope.new.patterns.push(val);
                    $scope.galleries.new.push({url: val.img});
                });
                angular.forEach($scope.new.add.patterns, function (val, key) {
                    $scope.new.patterns.push({id: null, arrId: key, img: val.img});
                    $scope.galleries.new.push({url: val.img});
                });
            },
            models: function () {
                var updated = [];
                angular.forEach($scope.new.update.models, function (val, key) {
                    updated.push(val);
                });
                return $scope.new.add.models.concat(updated);
            },
            projects: function () {
                var updated = [];
                angular.forEach($scope.new.update.projects, function (val, key) {
                    updated.push(val);
                });
                return $scope.new.add.projects.concat(updated);
            },
            add: {
                patterns: [],
                models: [],
                projects: []
            },
            update: {
                patterns: {},
                models: {},
                projects: {}
            }
        };
        $scope.change = {
            add: {
                pattern: function (event) {
                    var file = event.files[0], url, img;
                    if (file != undefined) {
                        url = URL.createObjectURL(file);
                        img = new Image;

                        img.onload = function () {
                            $('#pattern-loading').remove();
                            if (isImageFile(file)) {
                                if (cropper != undefined) {
                                    cropper.cropper('destroy');
                                }
                                cropper = $('.pattern-wrapper img').cropper({
                                    aspectRatio: getAspect(img),
                                    viewMode: 2,
                                    data: {x: 0, y: 0, width: img.width, height: img.height}
                                });
                                imgDef = {
                                    width: img.width,
                                    height: img.height
                                };
                            }
                        };

                        img.src = url;
                        img.style = 'display: none;';
                        $('.pattern-wrapper').html('<div id="pattern-loading"><i class="fa fa-spinner fa-spin fa-5x" ></i></div>').append(img);
                    }
                },
                model: function (event) {
                    $scope.new.add.models = [];
                    angular.forEach(event.files, function (file, key) {
                        $scope.new.add.models.push(file);
                    });
                    $scope.$apply();
                },
                project: function (event) {
                    $scope.new.add.projects = [];
                    angular.forEach(event.files, function (file, key) {
                        $scope.new.add.projects.push(file);
                    });
                    $scope.$apply();
                }
            },
            update: {
                pattern: function (event) {
                },
                model: function (event) {
                    var id = $(event).attr('data-id');
                    if (event.files.length > 0) {
                        $scope.new.update.models[id] = event.files[0];
                    } else {
                        delete $scope.new.update.models[id];
                    }
                    $scope.$apply();
                },
                project: function (event) {
                    var id = $(event).attr('data-id');
                    if (event.files.length > 0) {
                        $scope.new.update.projects[id] = event.files[0];
                    } else {
                        delete $scope.new.update.projects[id];
                    }
                    $scope.$apply();
                }
            }
        };
        $scope.delNewImage = function (key) {
            var img = $scope.new.patterns[key];
            if (img.id == null) {
                $scope.new.add.patterns.splice(img.arrId, 1);
            } else {
                delete $scope.new.update.patterns[img.id];
            }
            $scope.new.updPatterns();
        };

        $scope.selected = {
            client: null,
            order: null
        };
        $scope.ordersObj = {
            client: {
                change: function () {
                    if (!isEmpty($scope.selected.client)) {
                        $scope.selected.order = undefined;
                    }
                }
            },
            order: {
                change: function () {
                    if (!isEmpty($scope.selected.order) && isEmpty($scope.selected.client)) {
                        var next = true;
                        angular.forEach($scope.clients, function (client, key) {
                            if (next) {
                                if ($scope.selected.order.clientId == client.id) {
                                    $scope.selected.client = $scope.clients[$scope.selected.order.clientId];
                                    next = false;
                                }
                            }
                        });
                    }
                }
            },
            clear: function () {
                $scope.selected.client = undefined;
                $scope.selected.order = undefined;
            }
        };

        $scope.calendar = {
            dateCreation: {
                options: {
                    startingDay: 1
                },
                date: Date.now(),
                change: function () {
                    $scope.calendar.dateEnd.options.minDate = $scope.calendar.dateCreation.date;
                    if ($scope.calendar.dateEnd.date !== null
                        && getCropDate($scope.calendar.dateCreation.date) > getCropDate($scope.calendar.dateEnd.date)) {
                        $scope.calendar.dateEnd.date = null;
                    }
                },
                open: function () {
                    $scope.calendar.dateCreation.opened = true;
                },
                opened: false,
                tooltips: {
                    today: false,
                    clear: false,
                    calendar: false
                }
            },
            dateEnd: {
                options: {
                    startingDay: 1,
                    minDate: null
                },
                date: null,
                open: function () {
                    $scope.calendar.dateEnd.opened = true;
                },
                opened: false,
                tooltips: {
                    today: false,
                    clear: false,
                    calendar: false
                }
            },
            today: function (model) {
                if (model === $scope.calendar.dateCreation) {
                    $scope.calendar.dateCreation.date = getCropDate(Date.now());
                    if ($scope.calendar.dateEnd.date !== null
                        && getCropDate($scope.calendar.dateCreation.date) > getCropDate($scope.calendar.dateEnd.date)) {
                        $scope.calendar.dateEnd.date = getCropDate(Date.now());
                    }
                } else if (model === $scope.calendar.dateEnd) {
                    if ($scope.calendar.dateCreation.date !== null) {
                        if (getCropDate($scope.calendar.dateCreation.date) <= getCropDate(Date.now())) {
                            $scope.calendar.dateEnd.date = getCropDate(Date.now());
                        } else {
                            $scope.calendar.dateEnd.date = new Date($scope.calendar.dateCreation.date.valueOf());
                            $scope.calendar.dateEnd.date.setDate($scope.calendar.dateCreation.date.getDate() + 1);
                        }
                    }
                }
            },
            clear: function (model) {
                if (model === $scope.calendar.dateCreation) {
                    $scope.calendar.dateCreation.date = null;
                    $scope.calendar.dateEnd.date = null;
                } else {
                    model.date = null;
                    2
                }
            }
        };

        $scope.filterBy = function (item) {
            if (!isEmpty($scope.selected.client)) {
                return item.clientId == $scope.selected.client.id;
            } else {
                return true;
            }
        };

        $scope.switchEdit = function () {
            if ($scope.edit) {
                $scope.edit = false;
                $scope.actions = $scope.actionVariants.view;
                if (angular.toJson($scope.detail) != angular.toJson($scope.preview.detail)) {
                    angular.forEach($scope.preview.detail, function (val, key) {
                        if ($scope.detail[key] != val) {
                            if (typeof(val) === 'object') {
                                if (angular.toJson($scope.detail[key]) != angular.toJson(val)) {
                                    $scope.detail[key] = angular.copy(val);
                                }
                            } else {
                                $scope.detail[key] = val;
                            }
                        }
                    });
                }
                if (angular.toJson($scope.selected.client) != angular.toJson($scope.preview.selected.client)) {
                    $scope.selected.client = angular.copy($scope.preview.selected.client);
                }
                if (angular.toJson($scope.selected.order) != angular.toJson($scope.preview.selected.order)) {
                    $scope.selected.order = angular.copy($scope.preview.selected.order);
                }
                $scope.calendar.dateCreation.date =
                    $scope.calendar.dateEnd.options.minDate = new Date($scope.preview.detail.dateCreation);
                $scope.calendar.dateEnd.date = new Date($scope.preview.detail.dateEnd);
                clearValues();
            } else {
                $scope.edit = true;
                $scope.actions = $scope.actionVariants.edit;
            }
        };

        var modalInstance = null;
        $scope.modalActions = {
            close: function () {
                $('.pattern-wrapper').html('');
                $('#patternInput').val(null);
                if (cropper != undefined) {
                    cropper.cropper('destroy');
                }
                currImgId = null;
                imgDef = {
                    width: undefined,
                    height: undefined
                };

                modalInstance.dismiss();
                modalInstance = null;
            },
            aspect: {
                hA4: function () {
                    var data = {};
                    if (imgDef.width != undefined && imgDef.height != undefined) {
                        data = {x: 0, y: 0, width: imgDef.width, height: imgDef.height};
                    }
                    if (cropper != undefined) {
                        cropper.cropper('destroy');
                    }
                    cropper = $('.pattern-wrapper img').cropper({
                        aspectRatio: 29.7 / 21.0,
                        viewMode: 2,
                        data: data
                    });
                },
                vA4: function () {
                    var data = {};
                    if (imgDef.width != undefined && imgDef.height != undefined) {
                        data = {x: 0, y: 0, width: imgDef.width, height: imgDef.height};
                    }
                    if (cropper != undefined) {
                        cropper.cropper('destroy');
                    }
                    cropper = $('.pattern-wrapper img').cropper({
                        aspectRatio: 21.0 / 29.7,
                        viewMode: 2,
                        data: data
                    });
                },
                free: function () {
                    var data = {};
                    if (imgDef.width != undefined && imgDef.height != undefined) {
                        data = {x: 0, y: 0, width: imgDef.width, height: imgDef.height};
                    }
                    if (cropper != undefined) {
                        cropper.cropper('destroy');
                    }
                    cropper = $('.pattern-wrapper img').cropper({
                        aspectRatio: NaN,
                        viewMode: 2,
                        data: data
                    });
                }
            },
            rotate: {
                p90: function () {
                    if (cropper != undefined) cropper.cropper('rotate', 90);
                },
                m90: function () {
                    if (cropper != undefined) cropper.cropper('rotate', -90);
                },
                mirror: function () {
                    if (cropper != undefined) cropper.cropper('scaleX', cropper.cropper('getData')['scaleX'] * -1);
                }
            },
            update: function(imgId){
                if ($scope.new.update.patterns[imgId]) {
                    delete $scope.new.update.patterns[imgId];
                    $scope.new.updPatterns();
                } else {
                    currImgId = (imgId != undefined) ? imgId : null;
                    $scope.modal();
                }
            },
            save: function () {
                if (cropper != undefined) {
                    var dataURL = cropper.cropper('getCroppedCanvas').toDataURL('image/jpeg');
                    if (currImgId !== null) {
                        $scope.new.update.patterns[currImgId] = {
                            id: currImgId,
                            img: dataURL
                        };
                    } else {
                        $scope.new.add.patterns.push({
                            id: null,
                            img: dataURL
                        });
                    }
                    $scope.new.updPatterns();
                    $scope.modalActions.close();
                }
            }
        };
        $scope.modal = function () {
            modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'app/pages/nomenclature/modal/newPattern.html',
                size: 'lg',
                backdrop: 'static',
                scope: $scope
            });
        };

        $scope.saveData = function () {
            $scope.edit = false;
            $scope.actions = $scope.actionVariants.view;
            console.log('Saving data!');
        };

        $scope.refreshData = function () {
            if ($stateParams.id == null) {
                $window.history.back();
            }
            $scope.loading = true;
            if ($scope.edit) {
                $scope.edit = false;
                $scope.actions = $scope.actionVariants.view;
            }

            var $url = "/api/nomenclature/get-with-parents/" + $stateParams.id;
            $http.post($url, null, {headers: {'All-Versions': true}}).then(function successCallback(response) {
                var data = response.data;
                if (data.error) {
                    console.log(data);
                } else {
                    $scope.detail = data.data[0];
                    $scope.clients = data.clients;
                    $scope.groups = data.groups;
                    $scope.calendar.dateCreation.date =
                        $scope.calendar.dateEnd.options.minDate = new Date($scope.detail.dateCreation);
                    $scope.calendar.dateEnd.date = new Date($scope.detail.dateEnd);
                    angular.forEach($scope.clients, function (client, key) {
                        angular.forEach(client.orders, function (order, key) {
                            if ($scope.detail.orderId == order.id) {
                                $scope.selected.client = client;
                                $scope.selected.order = order;
                            }
                            $scope.orders = $scope.orders.concat(order);
                        });
                    });
                    $scope.preview = {
                        detail: angular.copy($scope.detail),
                        selected: {
                            client: angular.copy($scope.selected.client),
                            order: angular.copy($scope.selected.order)
                        }
                    };
                    clearValues();
                    $scope.galleries.main = [];
                    angular.forEach($scope.detail.pattern, function (image, key) {
                        $scope.galleries.main.push({
                            url: '/files/' + image.versions[0].id + '/' + image.name,
                            extUrl: '/files/' + image.versions[0].id + '/' + image.name
                        });
                        $scope.galleries[image.id] = [];
                        angular.forEach(image.versions, function (version, key) {
                            $scope.galleries[image.id].push({
                                url: '/files/' + version.id + '/' + image.name,
                                extUrl: '/files/' + version.id + '/' + image.name,
                                desText: version.date
                            });
                        });
                    });
                    $scope.loading = false;
                }
            }, function errorCallback(response) {
                console.log(response.statusText);
            });
        };

        $scope.refreshData();

        $scope.test = function () {
            var send = new FormData();
            angular.forEach($scope.new.models(), function (file, key) {
                send.append('models[]', file);
            });
            angular.forEach($scope.new.projects(), function (file, key) {
                send.append('projects[]', file);
            });
            var $url = "/api/nomenclature/get-with-parents/" + $stateParams.id;
            $http.post($url, send, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
            }).then(function successCallback(response) {
                console.log(response.data);
            });
        };
    }
})();