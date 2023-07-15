(function ($) {
    'use strict';
    var chunksData = {}
    var filePrefix = ""
    const DEFAULT_PADDING = 15
    const REPO_TREE_SEPERATOR = "|"
    const IMAGE_WIDTH = 50
    var ajaxCallIntervalID=0

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */


    $(function () {

        function showFileManager(logTargetSelector="") {
            console.log(chunksData)
            emptyResultLog(logTargetSelector)
            $("table#result-table tbody tr").remove();
            $("table#result-table").fadeIn();
            const firstLevelElementKeys = Object.keys(chunksData)
            const totalSize = getFolderSize(chunksData)
            const totalItems = getFolderItemsNumber(chunksData)
            const previousRepositoryTree = ""
            let firstLevelHTML = '<tr class="level-0">' +
                '<td></td>' +
                '<td style="display: flex;align-items: center;justify-content: stretch;gap: 10px;"' + '><img alt="" width="' + IMAGE_WIDTH + '" src="' + (diskUsageData.file_extension_uri) + 'data-server.png' + '" /> <span>Website Disk</span></td>' +
                '<td>100%</td>' +
                '<td>' + getFormattedSize(totalSize) + '</td>' +
                '<td>' + totalItems + '</td>' +
                '</tr>'
            firstLevelHTML += getTableLineTemplate(chunksData, firstLevelElementKeys, totalSize, previousRepositoryTree)
            $('#result-table tbody').append(firstLevelHTML)
            $("#showResultTab").trigger("click")
        }

        function getShowResultTableLine(isFile, fileName, actualElementSize, actualElementItemsNumber, percentage, level = "1", repositoryTree) {
            let template = "";
            const padding = DEFAULT_PADDING * (parseInt(level) - 1)
            template += '<tr data-is-folder="' + (!isFile ? "1" : "0") + '" data-repos-tree="' + (repositoryTree + (!!repositoryTree ? REPO_TREE_SEPERATOR : "") + fileName) + '" data-level="' + level + '" class="level-' + level + '">'
            template += '<td style="padding-left:' + padding + 'px" class="' + (!isFile ? "wp-updraft-disk-opener" : "") + '">' + (!isFile ? "+" : ".") + '</td>'
            template += '<td style="padding-left:' + padding + 'px;display: flex;align-items: center;justify-content: stretch;gap: 10px;"' + '> <img onerror="this.src=\'' + getBlankImageUrl() + '\'" alt="" width="' + IMAGE_WIDTH + '" src="' + getFileExtensionImageUrl(fileName, isFile) + '" /> <span>' + fileName + '</span></td>'
            template += '<td>' + percentage + ' %</td>'
            template += '<td>' + getFormattedSize(actualElementSize) + '</td>'
            template += '<td>' + (!!actualElementItemsNumber ? actualElementItemsNumber : "-") + '</td>'
            template += '</tr>'
            return template
        }

        function getFileExtensionImageUrl(fileName = "", isFile = true) {
            let extensionName = ""
            if (!isFile) {
                extensionName = "folder.png"
            } else {
                if (String(fileName).includes('.')) {
                    const splittedFileName = String(fileName).split('.')
                    const totalLength = splittedFileName.length
                    const extension = splittedFileName[totalLength - 1]
                    extensionName = extension + ".png"
                } else {
                    extensionName = "blank.png"
                }
            }
            return (diskUsageData.file_extension_uri) + extensionName
        }

        function getBlankImageUrl() {
            return (diskUsageData.file_extension_uri) + "blank.png"
        }

        function getTableLineTemplate(actualLevelData, actualElementKeys = [], totalSize, repositoryTree, level) {
            let template = ""
            actualElementKeys.forEach((firstLevelKey) => {
                const isFile = String(firstLevelKey).includes(filePrefix)
                const actualElementSize = (!!isFile ? (actualLevelData?.[firstLevelKey]).size : getFolderSize(actualLevelData?.[firstLevelKey]))
                const actualElementItemsNumber = !!isFile ? null : getFolderItemsNumber(actualLevelData?.[firstLevelKey])
                const percentage = (parseFloat(actualElementSize / totalSize) * 100).toFixed(1)
                const fileName = !!isFile ? (actualLevelData?.[firstLevelKey]).name : firstLevelKey
                template += getShowResultTableLine(isFile, fileName, actualElementSize,
                    actualElementItemsNumber, percentage, level, repositoryTree)
            })
            return template
        }

        $(document).on("click", ".wp-updraft-disk-opener", function () {
            let $this = $(this)
            let parentTr = $this.parent('tr[data-is-folder="1"]')
            const reposTree = parentTr.attr('data-repos-tree')
            if ($this.text() === '+') {
                let actualLevelKeys = {}
                let level = 0
                let totalSize = 0
                let actualLevelData = {...chunksData}
                if (!!reposTree) {
                    const reposTreeArray = String(reposTree).split(REPO_TREE_SEPERATOR)
                    level = reposTreeArray.length
                    if (level > 0) {
                        reposTreeArray.forEach((levelData) => {
                            actualLevelData = {...actualLevelData?.[levelData]}
                        })
                        totalSize = getFolderSize(actualLevelData)
                    }

                } else {
                    totalSize = getFolderSize(actualLevelData)
                }
                actualLevelKeys = Object.keys(actualLevelData)
                $this.text("-")

                $(getTableLineTemplate(actualLevelData, actualLevelKeys, totalSize, reposTree, level + 1)).insertAfter(parentTr)
            } else {
                parentTr.nextAll('tr[data-repos-tree^="' + reposTree + '"]').remove()
                $this.text("+")
            }

        })

        function getFolderSize(folder) {
            let sum = 0;
            for (const key of Object.keys(folder)) {
                const fileOrFolder = folder?.[key]
                if (String(key).includes(filePrefix)) {
                    sum += parseInt(fileOrFolder?.size)
                } else {
                    sum += getFolderSize(fileOrFolder);
                }
            }
            return sum;
        }

        function getFolderItemsNumber(folder) {
            let count = 0;
            for (const key of Object.keys(folder)) {
                const fileOrFolder = folder?.[key]
                if (String(key).includes(filePrefix)) {
                    count++
                } else {
                    count += getFolderItemsNumber(fileOrFolder);
                }
            }
            return count;
        }

        function getFormattedSize(size) {
            if (size === 0) {
                return 0 + " MB"
            }
            if (size < (1024 * 100)) {
                return (size / 1024).toFixed(1) + " KB"
            }
            return (size / 1024 / 1024).toFixed(1) + " MB"
        }

        function logResultGathering(message, targetSelector="#gather_result_log") {
            $(targetSelector).append('<p>' + message + ' ...</p>')
        }

        function emptyResultLog(targetSelector="#gather_result_log") {
            $(targetSelector+' p').remove()
        }


        // GATHERING RESULT AJAX HANDLING
        //console.log(diskUsageData);//working
        $(document).ready(function () {
            if ($("#showResult").length) {
                const nonce = $("#showResult").attr("data-nonce")
                startAjaxCall(nonce, true, "#showResult .log")
            }
        })


        function startAjaxCall(nonce, useSavedChunksDataOnly = false, logTargetSelector="") {
            const data = {
                action: "gather_disk_usage",
                nonce: nonce
            }
            $.ajax({
                type: 'post',
                dataType: "json",
                url: diskUsageData.ajax_url,
                cache: false,
                data: {
                    ...data,
                    ...(!!useSavedChunksDataOnly ? {
                        useSavedDataOnly:"1"
                    } : {})
                },
                success: async function (response) {
                    logResultGathering(response?.message || "N/A", logTargetSelector)
                    if (response?.status === '1' &&
                        !!response?.data?.timeout) {
                        //const chunksLength = parseInt(response?.data?.chunksLength)
                        const timeout = parseInt(response?.data?.timeout) * 1000
                        let newNonce = response?.data?.nonce
                        let chunkIndex = 0
                        filePrefix = response?.data?.file_prefix
                        await new Promise(resolve => {
                            ajaxCallIntervalID = setInterval(() => {
                                $.ajax({
                                    type: 'post',
                                    dataType: "json",
                                    url: diskUsageData.ajax_url,
                                    cache: false,
                                    data: {
                                        action: "gather_disk_usage",
                                        nonce: newNonce,
                                        chunkKey: chunkIndex,
                                        useSavedDataOnly:"1"
                                    },
                                    timeout: timeout,
                                    success: function (newResponse) {
                                        logResultGathering(newResponse?.message || "N/A", logTargetSelector)
                                        if (newResponse?.status === '1' &&
                                            newResponse?.data?.chunk &&
                                            newResponse?.data?.nonce

                                        ) {
                                            newNonce = newResponse?.data?.nonce
                                            chunksData = {
                                                ...chunksData,
                                                ...newResponse?.data?.chunk
                                            }
                                            if (newResponse?.data?.can_continue !== "1") {
                                                //console.log("Full Generated Data : ", chunksData)
                                                clearInterval(ajaxCallIntervalID)
                                                resolve(showFileManager(logTargetSelector))
                                            }
                                        } else {
                                            clearInterval(ajaxCallIntervalID)
                                        }
                                    }
                                })
                                chunkIndex += 1
                            }, timeout)
                        })
                    } else {
                        //something fishy happened
                    }
                }
            })
        }


        $("#gather-result").on("click", function (e) {
            e.preventDefault();
            if(!!ajaxCallIntervalID){
                clearInterval(ajaxCallIntervalID)
            }
            emptyResultLog("#gather_result_log");
            const $this = $(this)
            const nonce = $this.attr("data-nonce")
            startAjaxCall(nonce, false, "#gather_result_log")
        })


        // DEFAULT TAB HANDLING
        if ($('.defaultOpen').length) {
            $('.defaultOpen').addClass("active")
            $('.defaultOpen').trigger("click")

        }
    });


})(jQuery);


function openTab(evt, tabName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}


