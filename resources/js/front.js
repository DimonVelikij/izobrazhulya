app.controller('MainCtrl', function ($scope, $http) {
    $scope.images = {};
    var filteredHashTags = false,
        filteredColors = false;

    $scope.$watch('$viewContentLoaded', function() {
        $scope.loader = true;
        $http
            .get('/ajax/images')
            .then(function(response) {
                $scope.images = response.data.images;
                setShowAllImages();
                $scope.resetFilter();
                $scope.loader = false;
            });
    });

    function setShowAllImages() {
        for (var imageId in $scope.images) {
            if (!$scope.images.hasOwnProperty(imageId)) {
                continue;
            }
            $scope.images[imageId]['filtered'] = true;
        }
    }

    $scope.setColorFilter = function (color) {
        var index = $scope.colorFilter.indexOf(color);
        if (index == -1) {
            $scope.colorFilter.push(color);
        } else {
            $scope.colorFilter.splice(index, 1);
        }
    };
    
    $scope.setHashTagFilter = function (hash) {
        var hashFilter = [],
            isOrigin = false;
        if ($scope.hashFilter) {
            isOrigin = true;
            hashFilter = $scope.hashFilter.split(',');
        } else {
            hashFilter.push(hash)
        }
        if (hashFilter.indexOf(hash) == -1) {
            hashFilter.push(hash);
        } else if (isOrigin) {
            hashFilter.splice(hashFilter.indexOf(hash), 1   )
        }
        $scope.hashFilter = hashFilter.join();
    };

    $scope.resetFilter = function () {
        $scope.colorFilter = [];
        $scope.hashFilter = '';
        setShowAllImages();
    };

    $scope.$watch('hashFilter', function (newValue) {
        if (typeof newValue == 'undefined') return;
        if (newValue == 0) return hashTagFilter([]);
        filteredHashTags = true;
        var arrayFilter = newValue.split(',');
        hashTagFilter(arrayFilter);
    });

    $scope.$watch('colorFilter.length', function (newValue) {
        if (typeof newValue == 'undefined') return;
        if (newValue == 0)return colorFilter([]);
        filteredColors = true;
        var arrayFilter = $scope.colorFilter;
        colorFilter(arrayFilter);
    });

    function colorFilter(arrayFilter) {
        !arrayFilter.length ? $scope.resetFilter() : filter(arrayFilter, 'colors');
        if (filteredHashTags) {
            filteredColors = false;
            filteredHashTags = false;
            hashTagFilter($scope.hashFilter);
        }
    }

    function hashTagFilter(arrayFilter) {
        !arrayFilter.length ? $scope.resetFilter() : filter(arrayFilter, 'hash_tags');
        if (filteredColors) {
            filteredColors = false;
            filteredHashTags = false;
            colorFilter($scope.colorFilter);
        }
    }

    function filter(arrayFilter, prop) {
        for (var imageId in $scope.images) {
            if (!$scope.images.hasOwnProperty(imageId)) {
                continue;
            }
            for (var i = 0; i < $scope.images[imageId][prop].length; i++) {
                $scope.images[imageId]['filtered'] = !(arrayFilter.indexOf($scope.images[imageId][prop][i]) == -1);
                if ($scope.images[imageId]['filtered']) {
                    break;
                }
            }
        }
    }
    
    //
    $scope.pay = {};
    var prices = [];
    $scope.closeLayerPay = function () {
        $scope.layerPay = false;
        $scope.layerSuccess = false;
    };
    $scope.toPay = function(imageId) {
        $scope.summ = 0;
        prices = [];
        for (var image in $scope.images) {
            if (image == imageId) {
                $scope.payImage = $scope.images[imageId];
                $scope.payImage.id = imageId;
                break;
            }
        }
        if (!$scope.pay[imageId]) {
            return;
        }
        for (var price in $scope.pay[imageId]) {
            if ($scope.pay[imageId][price]) {
                $scope.summ += parseInt(price);
                prices.push(price);
            }
        }
        if (!$scope.summ) {
            return;
        }
        $scope.layerPay = true;
    };
    $scope.paySubmit = function () {
        var priceParams = '';
        for (var i = 0; i < prices.length; i++) {
            priceParams += '&price'+(i+1)+'='+parseInt(prices[i]);
        }
        var action = '/ajax/request/?image_id='+$scope.payImage.id+'&email='+$scope.email+'&cart='+$scope.cart+priceParams;

        $http
            .get(action)
            .then(function (response) {
                if (response.data.success) {
                    $scope.closeLayerPay();
                    $scope.layerSuccess = true;
                    delete $scope.pay[$scope.payImage.id];
                    $scope.payImage = {};
                }
            });
    };
});