(function(){
const c=document.createElement('canvas');
c.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999';
document.body.appendChild(c);
const x=c.getContext('2d');
let w,h,f=[];
function rs(){w=c.width=innerWidth;h=c.height=innerHeight}
addEventListener('resize',rs);rs();
for(let i=0;i<120;i++)f.push({x:Math.random()*w,y:Math.random()*h,s:Math.random()*1.2+.3,r:Math.random()*2.2+.8,o:Math.random()*.45+.35,d:Math.random()*.3-.15});
(function a(){
x.clearRect(0,0,w,h);
f.forEach(p=>{
p.y+=p.s;p.x+=p.d;
if(p.y>h){p.y=-4;p.x=Math.random()*w}
if(p.x>w)p.x=0;if(p.x<0)p.x=w;
x.beginPath();x.arc(p.x,p.y,p.r,0,6.28);
x.fillStyle=`rgba(255,255,255,${p.o})`;x.fill();
});
requestAnimationFrame(a);
})();
})();
