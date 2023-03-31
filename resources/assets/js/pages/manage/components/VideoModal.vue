<template>
    <Modal
        :value="visible"
        :mask="false"
        :mask-closable="false"
        :footer-hide="true"
        :beforeClose="onBeforeClose"
        class-name="video-modal"
        fullscreen>
        <div class="video-wrap" :style="wrapStyle">
            <div v-if="videoChatEnable" class="video-opacity">{{$L('正在视频通话...')}}</div>
            <div v-else class="video-opacity">{{$L('正在语音通话...')}}</div>
            <video ref="remoteVideo" class="video-active" autoplay></video>
            <video ref="localVideo" class="video-mini" autoplay muted @loadedmetadata="onLoadedmetadata"></video>
            <div class="video-close"><Icon type="ios-close-circle-outline" @click="onBeforeClose"/></div>
        </div>
    </Modal>
</template>

<style lang="scss">
body {
    .ivu-modal-wrap {
        &.video-modal {
            position: absolute;
            overflow: hidden;

            .ivu-modal {
                margin: 0;
                padding: 0;

                .ivu-modal-content {
                    background: transparent;

                    .ivu-modal-close {
                        display: none;
                    }

                    .ivu-modal-body {
                        padding: 0;
                        display: flex;
                        flex-direction: column;
                        overflow: hidden;
                    }
                }
            }
        }
    }
}
</style>
<style lang="scss" scoped>
.video-wrap {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #000000;
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;

    &:before {
        content: "";
        position: absolute;
        left: -10%;
        right: -10%;
        top: -10%;
        bottom: -10%;
        background: inherit;
        filter: blur(25px);
        z-index: 1;
    }

    &:after {
        content: "";
        position: absolute;
        left: -10%;
        right: -10%;
        top: -10%;
        bottom: -10%;
        background: rgba(0, 0, 0, 0.82);
        z-index: 2;
    }

    .video-opacity {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 26px;
        color: #aaaaaa;
        padding: 24px;
        z-index: 3;
        animation:opacity 2s infinite alternate ;
        @keyframes opacity {
            0% {
                opacity: 0.1;
            }
            100% {
                opacity: 1;
            }
        }
    }

    .video-mini,
    .video-active {
        position: absolute;
        max-width: 640px;
        max-height: 100%;
        object-fit: cover;
        transition: opacity 1s;
    }

    .video-active {
        top: 0;
        left: 50%;
        width: 100%;
        height: 100%;
        transform: rotateY(180deg) translateX(50%);
        z-index: 4;
    }

    .video-mini {
        top: 0;
        right: 0;
        width: 260px;
        height: 180px;
        transform: scale(-1, 1);
        z-index: 5;
    }

    .video-close {
        position: absolute;
        max-width: 720px;
        bottom: 18px;
        left: 50%;
        width: 100%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 6;
        > i {
            font-weight: 600;
            font-size: 46px;
            color: #ffffff;
            cursor: pointer;
            transition: color 0.3s;
            &:hover {
                color: #ff0000;
            }
        }
    }
}
</style>

<script>
import {mapState} from "vuex";

export default {
    name: "VideoModal",

    data() {
        return {

        }
    },

    computed: {
        ...mapState([
            'wsMsg',

            'videoChatEnable',
            'videoLocalStream',
            'videoRemoteUserid',

            'cacheUserBasic'
        ]),

        visible() {
            return this.videoLocalStream !== null
        },

        wrapStyle() {
            const user = this.cacheUserBasic.find(({userid}) => userid == this.videoRemoteUserid);
            if (user) {
                return {
                    backgroundImage: `url("${user.userimg}")`
                }
            }
            return null;
        }
    },

    watch: {
        visible(visible) {
            this.$nextTick(_ => {
                if (this.$refs.localVideo) {
                    this.$refs.localVideo.srcObject = visible ? this.videoLocalStream : null
                }
            })
        },

        wsMsg: {
            handler(info) {
                const {type, action} = info;
                switch (type) {
                    case 'video':
                        if (action == 'call') {
                            this.$store.dispatch("audioPlay", {
                                src: $A.originUrl("audio/call.wav"),
                                loop: true
                            })
                        }
                        break;
                }
            },
            deep: true,
        },
    },

    methods: {
        onBeforeClose() {
            return new Promise(async resolve => {
                await this.$store.dispatch('closeVideo')
                resolve(true)
            })
        },

        onLoadedmetadata() {
            // 本地加载完成 通知对方
            this.$store.dispatch("websocketSend", {
                type: 'sendMsg',
                data: {
                    userid: this.videoRemoteUserid,
                    msg: {
                        type: 'video',
                        action: 'call',
                        enable: this.videoChatEnable,
                    },
                },
                callback: data => {
                    if ($A.isError(data)) {
                        $A.modalError(data.msg || '发起失败');
                        this.onBeforeClose()
                    }
                }
            }).catch(_ => {
                $A.modalError('发起失败');
                this.onBeforeClose()
            })
        }
    }
}
</script>
