document.getElementById('close').onmousedown = function (e) {
    e.preventDefault();
    document.getElementById('info').style.display = 'none';
    return false;
};

/* Settings */

var MOUSE_INFLUENCE = 5,
    GRAVITY_X = 0,
    GRAVITY_Y = 9.8,
    MOUSE_REPEL = false,
    GROUPS = [50, 50, 50],
    GROUP_COLOURS = ['rgba(97,160,232'];
var isClick=false;
var isFirst = true;
var bubbles=document.getElementById('bubbles');
bubbles.style.display='none'
var blue0='rgba(8,117,239,0)';
var blue1='rgba(8,117,239,1)'; //blue'
if (typeof Type == "undefined") {
    var Type = {};
    Type.Litmus = 0;
    Type.HCL = 1;
    Type.NaOH = 2;
    Type.NaCO3 = 3;
}

window.requestAnimFrame =
    window.requestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    window.oRequestAnimationFrame ||
    window.msRequestAnimationFrame ||
    function (callback) {
        window.setTimeout(callback, 1000 / 60);
    };

var fluid = function () {

    var ctx, width, height, num_x, num_y, particles, grid, meta_ctx, threshold = 220, play = false, spacing = 45, radius = 40, limit = radius * 0.66, textures, num_particles;

    //液滴数组
    var lqs = [];

    //已加入的试剂类型
    var types = Array();

    var mouse = {
        down: false,
        x: 0,
        y: 0
    };

    var process_image = function () {
        var imageData = meta_ctx.getImageData(0, 0, width, height),
            pix = imageData.data;

        for (var i = 0, n = pix.length; i < n; i += 4) {
            (pix[i + 3] < threshold) && (pix[i + 3] /= 6);
        }

        ctx.putImageData(imageData, 0, 0);
    };

    var run = function () {

        //var time = new Date().getTime();
        //清空，等待重绘
        meta_ctx.clearRect(0, 0, width, height);

        for (var i = 0, l = num_x * num_y; i < l; i++) grid[i].length = 0;


        var i = num_particles;
        while (i--) particles[i].first_process();
        i = num_particles;
        while (i--) particles[i].second_process();

        process_image();

        //绘制鼠标焦点(可注释)
        if (mouse.down) {

            ctx.canvas.style.cursor = 'none';

            ctx.fillStyle = 'rgba(97, 160, 232, 0.05)';
            ctx.beginPath();
            ctx.arc(
                mouse.x,
                mouse.y,
                radius * MOUSE_INFLUENCE,
                0,
                Math.PI * 2
            );
            ctx.closePath();
            ctx.fill();

            ctx.fillStyle = 'rgba(97, 160, 232, 0.05)';
            ctx.beginPath();
            ctx.arc(
                mouse.x,
                mouse.y,
                (radius * MOUSE_INFLUENCE) / 3,
                0,
                Math.PI * 2
            );
            ctx.closePath();
            ctx.fill();
        } else ctx.canvas.style.cursor = 'default';

        //处理水滴落下
        if (lqs) {

            for (var j = 0; j < lqs.length; j++) {
                var li = lqs[j];
                if (li.down && li.y < window.innerHeight / 1.7) {

                    li.vx += GRAVITY_X;
                    li.vy += 1;
                    li.px = li.x;
                    li.py = li.y;
                    li.x += li.vx;
                    li.y += li.vy;
                    li.draw();
                } else {
                    li.down = false;
                    if(isClick)
                    { isClick=false;
                        hasReaction(li);
                        var i = num_particles;
                        while (i--) particles[i].second_process();

                    }

                }
            }
        }
        //console.log(new Date().getTime() - time);
        if (play)
            requestAnimFrame(run);
    };

    //FIXME 修改反应后的颜色
    var hasReaction = function (li) {
        var color1 = li.color + ',1)', color2 = li.color + ',0)';
        console.log(li.type)
        switch (li.type) {

            case Type.Litmus:
                if (types.indexOf(Type.HCL) > -1) {

                    //  GROUP_COLOURS = ['rgba(132,19,180']; //紫
                    // 152 45 40 //red
                    //  98 67 127 //blue
                    // 208 ,207, 207 //white
                    // 117,88,151  lightblue
                    color1 = 'rgba(152,45,40,1)';
                    color2 = 'rgba(152,45,40,0)';
                } else if (types.indexOf(Type.NaOH) > -1) {
                    color1 = blue1;
                    color2 = blue0;
                }  else if (types.indexOf(Type.NaCO3) > -1) {
                    color1 = 'rgba(117,88,151,1)';
                    color2 = 'rgba(117,88,151,0)';
                }

                console.log(types)
                break;
            case Type.HCL:
                if (types.indexOf(Type.Litmus) > -1) {
                    color1 = 'rgba(152 ,45 ,40,1)';
                    color2 = 'rgba(152 ,45, 40,0)';
                }
                if (types.indexOf(Type.NaOH) > -1) {

                    if(types.indexOf(Type.Litmus) > -1)
                    {
                        color1 =   'rgba(132,19,180,1)';
                        color2 =   'rgba(132,19,180,0)';
                    }
                    else{
                        color1 = 'rgba(208 ,207, 207,1)';
                        color2 = 'rgba(208 ,207, 207,0)';
                    }
                    // HCL NaOH
                    types.splice(types.indexOf(Type.HCL),1)
                    types.splice(types.indexOf(Type.NaOH),1)
                }
                if (types.indexOf(Type.NaCO3) > -1) {
                    bubbles.style.display='block'
                    // todo 气泡
                    if(types.indexOf(Type.Litmus) > -1)
                    {
                        color1 =   'rgba(132,19,180,1)';
                        color2 =   'rgba(132,19,180,0)';
                    }
                    else{
                        color1 = 'rgba(208 ,207, 207,1)';
                        color2 = 'rgba(208 ,207, 207,0)';
                    }
                    // HCL NaOH
                    if(types.indexOf(Type.HCL) > -1)
                    {
                        types.splice(types.indexOf(Type.HCL),1)

                    }

                    types.splice(types.indexOf(Type.NaCO3),1)
                }


                console.log(types)
                break;
            case Type.NaCO3:
                if (types.indexOf(Type.NaOH) > -1) {
                    // color1 = 'rgba(0,0,0,1)';
                    // color2 = 'rgba(0,0,0,0)';
                } else if (types.indexOf(Type.HCL) > -1) {
                    bubbles.style.display='block'
                    // todo 气泡
                    if (types.indexOf(Type.Litmus) > -1) {
                        color1 = 'rgba(132,19,180,1)';
                        color2 = 'rgba(132,19,180,0)';
                    }
                    else {
                        color1 = 'rgba(208 ,207, 207,1)';
                        color2 = 'rgba(208 ,207, 207,0)';
                    }
                    // HCL NaOH
                    types.splice(types.indexOf(Type.HCL), 1)
                    types.splice(types.indexOf(Type.NaCO3), 1)
                }
                else if(types.indexOf(Type.Litmus) > -1)
                {
                    color1 = blue1;
                    color2 = blue0;
                }
                console.log(types)
                break;
            case Type.NaOH:


                if (types.indexOf(Type.NaCO3) > -1) {
                    color1 = blue1;
                    color2 = blue0;
                } else if (types.indexOf(Type.HCL) > -1) {
                    if (types.indexOf(Type.Litmus) > -1) {
                        color1 = 'rgba(132,19,180,1)';
                        color2 = 'rgba(132,19,180,0)';
                    }
                    else {
                        color1 = 'rgba(208 ,207, 207,1)';
                        color2 = 'rgba(208 ,207, 207,0)';
                    }
                    // HCL NaOH
                    types.splice(types.indexOf(Type.HCL), 1)
                    types.splice(types.indexOf(Type.NaOH), 1)
                }
                else if (types.indexOf(Type.Litmus) > -1) {
                    color1 = blue1;
                    color2 = blue0;
                }
                console.log(types)
                break;

        }
        hybrid(color1, color2);
    };

    // 混合（改变颜色）
    var hybrid = function (color1, color2) {
        for (var i = 0; i < GROUPS.length; i++) {
            var colour;
            // if (GROUP_COLOURS[i]) {
            //colour = color;
            // } else {
            // colour =
            // 'hsla(' + Math.round(Math.random() * 360) + ', 80%, 60%';
            // }
            textures[i] = document.createElement("canvas");
            textures[i].width = radius * 2;
            textures[i].height = radius * 2;
            var nctx = textures[i].getContext("2d");  // getContext("2d") 对象是内建的 HTML5 对象，拥有多种绘制路径、矩形、圆形、字符以及添加图像的方法。
            var grad = nctx.createRadialGradient(
                radius,
                radius,
                1,
                radius,
                radius,
                radius
            );

            //grad.addColorStop(0, colour + ',1)');
            //grad.addColorStop(1, colour + ',0)');
            grad.addColorStop(0, color1);
            grad.addColorStop(1, color2);
            nctx.fillStyle = grad;
            nctx.beginPath();
            nctx.arc(radius, radius, radius, 0, Math.PI * 2, true);
            nctx.closePath();
            nctx.fill();
        }
    };

    var liquid = function (color, type) {
        console.log('init liquid');
        this.type = type;  //试剂类型
        this.down = true;  //默认还没滴下水滴
        this.radius = 25;
        this.color = color;  //水滴颜色
        this.x = window.innerWidth / 2;  //水平居中显示
        this.y = 10;
        this.px = this.x;
        this.py = this.y;
        this.vx = 0;
        this.vy = 0;

        if (types.indexOf(type)>-1) {

        } else {
            types.push(type);
        }
    };

    liquid.prototype.draw = function () {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2, true);
        ctx.closePath();
        ctx.fillStyle = this.color + ',1)';
        ctx.fill();
    };

    // 粒子
    var Particle = function (type, x, y) {
        this.type = type;
        this.x = x;
        this.y = y;
        this.px = x;
        this.py = y;
        this.vx = 0;
        this.vy = 0;
    };

    Particle.prototype.first_process = function () {

        var g = grid[Math.round(this.y / spacing) * num_x + Math.round(this.x / spacing)];

        if (g) g.close[g.length++] = this;

        this.vx = this.x - this.px;
        this.vy = this.y - this.py;

        if (mouse.down) {
            var dist_x = this.x - mouse.x;
            var dist_y = this.y - mouse.y;
            var dist = Math.sqrt(dist_x * dist_x + dist_y * dist_y);
            if (dist < radius * MOUSE_INFLUENCE) {
                var cos = dist_x / dist;
                var sin = dist_y / dist;
                this.vx += (MOUSE_REPEL) ? cos : -cos;
                this.vy += (MOUSE_REPEL) ? sin : -sin;
            }
        }

        this.vx += GRAVITY_X;
        this.vy += GRAVITY_Y;
        this.px = this.x;
        this.py = this.y;
        this.x += this.vx;
        this.y += this.vy;
    };

    Particle.prototype.second_process = function () {

        var force = 0,
            force_b = 0,
            cell_x = Math.round(this.x / spacing),
            cell_y = Math.round(this.y / spacing),
            close = [];

        for (var x_off = -1; x_off < 2; x_off++) {
            for (var y_off = -1; y_off < 2; y_off++) {
                var cell = grid[(cell_y + y_off) * num_x + (cell_x + x_off)];
                if (cell && cell.length) {
                    for (var a = 0, l = cell.length; a < l; a++) {
                        var particle = cell.close[a];
                        if (particle != this) {
                            var dfx = particle.x - this.x;
                            var dfy = particle.y - this.y;
                            var distance = Math.sqrt(dfx * dfx + dfy * dfy);
                            if (distance < spacing) {
                                var m = 1 - (distance / spacing);
                                force += Math.pow(m, 2);
                                force_b += Math.pow(m, 3) / 2;
                                particle.m = m;
                                particle.dfx = (dfx / distance) * m;
                                particle.dfy = (dfy / distance) * m;
                                close.push(particle);
                            }
                        }
                    }
                }
            }
        }

        force = (force - 3) * 0.5;

        for (var i = 0, l = close.length; i < l; i++) {

            var neighbor = close[i];

            var press = force + force_b * neighbor.m;
            if (this.type != neighbor.type) press *= 0.35;

            var dx = neighbor.dfx * press * 0.5;
            var dy = neighbor.dfy * press * 0.5;

            neighbor.x += dx;
            neighbor.y += dy;
            this.x -= dx;
            this.y -= dy;
        }

        if (this.x < limit) this.x = limit;
        else if (this.x > width - limit) this.x = width - limit;

        if (this.y < limit) this.y = limit;
        else if (this.y > height - limit) this.y = height - limit;

        this.draw();
    };

    Particle.prototype.draw = function () {

        var size = radius * 2;

        meta_ctx.drawImage(
            textures[this.type],
            this.x - radius,
            this.y - radius,
            size,
            size);
    };

    return {

        init: function (canvas, w, h,type) {
            isFirst = false;
            particles = [];
            grid = [];
            close = [];
            textures = [];
            types.push(type);

            var canvas = document.getElementById(canvas);
            ctx = canvas.getContext('2d');
            canvas.height = h || window.innerHeight / 2;
            canvas.width = w || window.innerWidth;
            width = canvas.width;
            height = canvas.height;

            var meta_canvas = document.createElement("canvas");
            meta_canvas.width = width;
            meta_canvas.height = height;
            meta_ctx = meta_canvas.getContext("2d");

            for (var i = 0; i < GROUPS.length; i++) {

                var colour;

                if (GROUP_COLOURS[i]) {
                    colour = GROUP_COLOURS[i];
                    // colour = '#00000000';  //透明
                } else {
                    // colour =
                    // 'hsla(' + Math.round(Math.random() * 360) + ', 80%, 60%';
                }

                textures[i] = document.createElement("canvas");
                textures[i].width = radius * 2;
                textures[i].height = radius * 2;
                var nctx = textures[i].getContext("2d");  // getContext("2d") 对象是内建的 HTML5 对象，拥有多种绘制路径、矩形、圆形、字符以及添加图像的方法。

                var grad = nctx.createRadialGradient(
                    radius,
                    radius,
                    1,
                    radius,
                    radius,
                    radius
                );

                grad.addColorStop(0, colour + ',1)');
                grad.addColorStop(1, colour + ',0)');
                nctx.fillStyle = grad;
                //nctx.fillStyle = 'rgba(0,0,0,0)';
                nctx.beginPath();
                nctx.arc(radius, radius, radius, 0, Math.PI * 2, true);
                nctx.closePath();
                nctx.fill();
            }

            canvas.onmousedown = function (e) {
                mouse.down = true;
                return false;
            };

            canvas.onmouseup = function (e) {
                mouse.down = false;
                return false;
            };

            canvas.onmousemove = function (e) {
                var rect = canvas.getBoundingClientRect();
                mouse.x = e.clientX - rect.left;
                mouse.y = e.clientY - rect.top;
                return false;
            };

            num_x = Math.round(width / spacing) + 1;
            num_y = Math.round(height / spacing) + 1;

            for (var i = 0; i < num_x * num_y; i++) {
                grid[i] = {
                    length: 0,
                    close: []
                }
            }

            for (var i = 0; i < GROUPS.length; i++) {
                for (var k = 0; k < GROUPS[i]; k++) {
                    particles.push(
                        new Particle(
                            i,
                            radius + Math.random() * (width - radius * 2),
                            radius + Math.random() * (height - radius * 2)
                        )
                    );
                }
            }

            num_particles = particles.length;

            play = true;
            run();
        },

        stop: function () {
            play = false;
        },

        //FIXME 修改试剂颜色
        insert: function (type) {
            bubbles.style.display='none'
            switch (type) {
                case Type.Litmus:
                    //lqs.push(new liquid('rgba(132,19,180', type));
                    GROUP_COLOURS = ['rgba(132,19,180'];
                    break;
                case Type.HCL:
                    //lqs.push(new liquid('rgba(255,255,255', type));
                    GROUP_COLOURS = ['rgba(255,255,255'];
                    break;
                case Type.NaCO3:
                    //lqs.push(new liquid('rgba(255,255,255', type));
                    GROUP_COLOURS = ['rgba(255,255,255'];
                    break;
                case Type.NaOH:
                    //lqs.push(new liquid('rgba(255,255,255', type));
                    GROUP_COLOURS = ['rgba(255,255,255'];
                    break;
            }
            isClick=true;
            if (isFirst)
                fluid.init('c',undefined,undefined,type);
            else{
                lqs[0]=(new liquid(GROUP_COLOURS[0], type));
            }
        }
    };
}();

//fluid.init('c');

document.getElementById('reset').onmousedown = function () {
    fluid.stop();
    setTimeout(function () {
        fluid.init('c')
    }, 100);
};
