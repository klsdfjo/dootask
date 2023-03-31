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
            <div v-if="videoEnable" class="video-opacity">{{$L('正在视频通话...')}}</div>
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
    .chat-notice-box {
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        .chat-notice-userimg {
            width: 42px;
            height: 42px;
            font-size: 20px;
            line-height: 42px;
            border-radius: 4px;
        }
        .ivu-notice-with-desc {
            flex: 1;
            padding: 0 12px;
        }
        .chat-notice-btn-box {
            margin-top: 8px;
            margin-bottom: -4px;
            .ivu-btn {
                margin-right: 12px;
                font-size: 12px;
                min-width: 42px;
            }
        }
        .ivu-notice-desc {
            font-size: 13px;
            word-break: break-all;
            line-height: 1.3;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            overflow: hidden;
            -webkit-box-orient: vertical;
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
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #aaaaaa;
        padding: 0 24px;
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
            localStream: null,
            remoteStream: null,

            rtcPeer: null,
            startTime: 0,
        }
    },

    computed: {
        ...mapState([
            'wsMsg',
            'userInfo',

            'videoUserid',
            'videoOwner',
            'videoEnable',

            'cacheUserBasic'
        ]),

        visible() {
            return this.videoUserid > 0
        },

        wrapStyle() {
            const user = this.cacheUserBasic.find(({userid}) => userid == this.videoUserid);
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
            if (visible) {
                window.navigator.getUserMedia = window.navigator.getUserMedia || window.navigator.webkitGetUserMedia || window.navigator.mozGetUserMedia;
                try {
                    window.navigator.mediaDevices.getUserMedia({
                        audio: true,
                        video: !!this.videoEnable
                    }).then(stream => {
                        this.localStream = stream;
                    }).catch(_ => {
                        $A.modalWarning({
                            content: '当前环境不支持音视频通话！',
                            onOk: this.onBeforeClose,
                        })
                    })
                } catch (e) {
                    $A.modalWarning({
                        content: '当前环境不支持音视频通话！',
                        onOk: this.onBeforeClose,
                    })
                }
            } else {
                if (this.localStream !== null) {
                    this.localStream.getTracks().forEach(track => {
                        track.stop()
                    })
                    this.localStream = null
                }
                if (this.startTime > 0) {
                    // todo 发送通话时长
                    this.startTime = 0
                }
            }
        },

        localStream() {
            this.$nextTick(_ => {
                if (this.$refs.localVideo) {
                    this.$refs.localVideo.srcObject = this.localStream
                }
            })
        },

        wsMsg: {
            handler(info) {
                const {type, action} = info;
                switch (type) {
                    case 'video':
                        if (action == 'call') {
                            this.handleMsgCall(info)
                        } else if (action == 'close') {
                            this.handleMsgClose(info)
                        } else if (action == 'offer') {
                            this.handleMsgOffer(info)
                        } else if (action == 'answer') {
                            this.handleMsgAnswer(info)
                        } else if (action == 'candidate') {
                            this.handleMsgCandidate(info)
                        }
                        break;
                }
            },
            deep: true,
        },
    },

    methods: {
        // 关闭窗口前
        onBeforeClose() {
            return new Promise(resolve => {
                this.onClose(this.videoUserid)
                resolve(true)
            })
        },

        // 关闭窗口
        onClose(sendMsgUserid, closeVideo = true) {
            if (sendMsgUserid > 0) {
                this.$store.dispatch("websocketSend", {
                    type: 'sendMsg',
                    data: {
                        userid: sendMsgUserid,
                        msg: {
                            type: 'video',
                            action: 'close',
                            user: {
                                userid: this.userId,
                                userimg: this.userInfo.userimg,
                                nickname: this.userInfo.nickname,
                            },
                        },
                    }
                })
            }
            if (closeVideo) {
                this.$store.dispatch('closeVideo')
            }
        },

        onError(content) {
            $A.modalError({
                content,
                onOk: this.onBeforeClose,
            })
        },

        // 本地加载完成
        onLoadedmetadata() {
            if (this.videoOwner) {
                // 发起者（通知对方）
                this.$store.dispatch("websocketSend", {
                    type: 'sendMsg',
                    data: {
                        userid: this.videoUserid,
                        msg: {
                            type: 'video',
                            action: 'call',
                            enable: this.videoEnable,
                            user: {
                                userid: this.userId,
                                userimg: this.userInfo.userimg,
                                nickname: this.userInfo.nickname,
                            },
                        },
                    },
                    callback: data => {
                        if ($A.isError(data)) {
                            this.onError(data.msg || '发起失败')
                        }
                    }
                }).catch(this.onError)
            } else {
                // 接收者（接受通话）
                this.onIcecandidate()
                this.rtcPeer.createOffer({
                    offerToReceiveAudio: true,
                    offerToReceiveVideo: this.videoEnable,
                }).then(desc => {
                    this.rtcPeer.setLocalDescription(desc).then(_ => {
                        this.$store.dispatch("websocketSend", {
                            type: 'sendMsg',
                            data: {
                                userid: this.videoUserid,
                                msg: {
                                    type: 'video',
                                    action: 'offer',
                                    data: this.rtcPeer.localDescription,
                                },
                            }
                        })
                    }).catch(this.onError)
                }).catch(this.onError)
            }
        },

        onIcecandidate() {
            this.rtcPeer = new RTCPeerConnection({
                "iceServers": [{
                    "urls": ["turn:business.swoole.com:3478?transport=udp", "turn:business.swoole.com:3478?transport=tcp"],
                    "username": "ceshi",
                    "credential": "ceshi"
                }]
            });
            this.rtcPeer.onicecandidate = (event) => {
                if (event.candidate) {
                    this.$store.dispatch("websocketSend", {
                        type: 'sendMsg',
                        data: {
                            userid: this.videoUserid,
                            msg: {
                                type: 'video',
                                action: 'candidate',
                                data: event.candidate,
                            },
                        }
                    })
                }
            };
            try {
                this.rtcPeer.addStream(this.localStream);
            } catch (e) {
                const tracks = this.localStream.getTracks();
                for (let i = 0; i < tracks.length; i++) {
                    this.rtcPeer.addTrack(tracks[i], this.localStream);
                }
            }
            this.rtcPeer.onaddstream = (e) => {
                this.$refs.remoteVideo.srcObject = e.stream;
            };
        },

        // 呼叫消息
        handleMsgCall({user, enable}) {
            // 如果当前正在通话，直接关闭
            if (this.visible) {
                this.onClose(user.userid)
                return
            }
            // 开始播放提示音
            this.$store.dispatch("audioPlay", {
                src: $A.originUrl("audio/call.wav"),
                loop: true
            })
            //  显示提示框
            this.$Notice.close('chat-call')
            this.$Notice.open({
                name: 'chat-call',
                duration: 0,
                onClose: () => {
                    // 关闭提示框
                    this.$Notice.close('chat-call')
                    this.$store.dispatch("audioStop", true)
                    this.onClose(user.userid)
                },
                render: h => {
                    return h('div', {
                        class: 'chat-notice-box',
                    }, [
                        h('EAvatar', {class: 'chat-notice-userimg', props: {src: user.userimg}}),
                        h('div', {class: 'ivu-notice-with-desc'}, [
                            h('div', {class: 'ivu-notice-title'}, user.nickname),
                            h('div', {class: 'ivu-notice-desc'}, this.$L(enable ? "邀请视频通话..." : "邀请语音通话...")),
                            h('div', {class: 'chat-notice-btn-box'}, [
                                h('Button', {
                                    props: {type: 'success', size: 'small'},
                                    on: {
                                        click: () => {
                                            // 接受通话
                                            this.$Notice.close('chat-call')
                                            this.$store.dispatch("audioStop", true)
                                            this.$store.dispatch("openVideo", {userid: user.userid, enable, owner: false})
                                        }
                                    }
                                }, this.$L("接受")),
                                h('Button', {
                                    props: {type: 'error', size: 'small'},
                                    on: {
                                        click: () => {
                                            // 拒绝通话
                                            this.$Notice.close('chat-call')
                                            this.$store.dispatch("audioStop", true)
                                            this.onClose(user.userid)
                                        }
                                    }
                                }, this.$L("拒绝")),
                            ])
                        ])
                    ])
                }
            });
            // todo 呼叫超时处理
        },

        // 关闭消息
        handleMsgClose({user}) {
            if (this.visible) {
                // 正在通话中
                if (user.userid == this.videoUserid) {
                    this.onClose(0)
                } else {
                    this.onClose(user.userid, false)
                }
            } else {
                // 没有通话中
                this.$Notice.close('chat-call')
                this.$store.dispatch("audioStop", true)
            }
        },

        // 提供消息
        handleMsgOffer({data}) {
            this.onIcecandidate()
            this.rtcPeer.setRemoteDescription(new RTCSessionDescription(data)).then(() => {
                if (this.startTime === 0) {
                    this.startTime = Math.round(new Date().getTime() / 1000)
                    this.rtcPeer.createAnswer().then(desc => {
                        this.rtcPeer.setLocalDescription(desc).then(_ => {
                            this.$store.dispatch("websocketSend", {
                                type: 'sendMsg',
                                data: {
                                    userid: this.videoUserid,
                                    msg: {
                                        type: 'video',
                                        action: 'answer',
                                        data: this.rtcPeer.localDescription,
                                    },
                                }
                            })
                        }).catch(this.onError)
                    }).catch(this.onError)
                }
            }).catch(this.onError)
        },

        // 应答消息
        handleMsgAnswer({data}) {
            if (this.rtcPeer) {
                this.rtcPeer.setRemoteDescription(new RTCSessionDescription(data)).catch(this.onError)
            }
        },

        // 候选消息
        handleMsgCandidate({data}) {
            if (this.rtcPeer) {
                this.rtcPeer.addIceCandidate(new RTCIceCandidate(data)).catch(this.onError)
            }
        },
    }
}
</script>
