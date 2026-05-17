// POWERFIT GYM ADMIN JS
async function sha256(s){const b=await crypto.subtle.digest('SHA-256',new TextEncoder().encode(s));return Array.from(new Uint8Array(b)).map(x=>x.toString(16).padStart(2,'0')).join('');}
function checkAuth(){const s=JSON.parse(sessionStorage.getItem('wg_admin_session')||'null');if(!s||Date.now()>s.expires)window.location.href='login.html';}
function handleLogout(){sessionStorage.removeItem('wg_admin_session');window.location.href='login.html';}
function showToast(msg,type='success'){let c=document.getElementById('toastContainer');if(!c){c=document.createElement('div');c.id='toastContainer';c.className='toast-container';document.body.appendChild(c);}const t=document.createElement('div');t.className='toast '+type;t.textContent=msg;c.appendChild(t);setTimeout(()=>t.remove(),3500);}
function saveData(key,data){localStorage.setItem('wg_'+key,JSON.stringify(data));showToast('Perubahan berhasil disimpan!');}
function loadData(key){try{const s=localStorage.getItem('wg_'+key);return s?JSON.parse(s):null;}catch(e){return null;}}
function v(id){const el=document.getElementById(id);return el?el.value:'';}
function getData(key){return loadData(key)||JSON.parse(JSON.stringify(defaults[key]));}
function switchPanel(id){document.querySelectorAll('.editor-panel').forEach(p=>p.classList.remove('active'));document.querySelectorAll('.sidebar-item').forEach(i=>i.classList.remove('active'));const p=document.getElementById('panel-'+id);if(p)p.classList.add('active');document.querySelectorAll('[data-panel="'+id+'"]').forEach(i=>i.classList.add('active'));}

const defaults={
  profil:{gym_name:'PowerFit Gym',tagline:'Transform Your Body, Transform Your Life',history:'PowerFit Gym berdiri sejak 2018 dengan misi memberikan fasilitas kebugaran terbaik.',vision:'Menjadi pusat kebugaran pilihan utama di kota.',mission:'Memberikan layanan kebugaran profesional yang terjangkau dan berkualitas.',address:'Jl. Sudirman No. 123, Jakarta Selatan',phone:'+62 812-3456-7890',email:'info@powerfitgym.com',whatsapp:'6281234567890',maps_embed:'',maps_link:'https://maps.google.com'},
  packages:[
    {id:1,name:'Paket Harian',duration:'1 Hari',price:50000,description:'Akses gym untuk 1 hari penuh.',benefits:['Akses area utama','Loker gratis'],display_order:1,is_active:true,featured:false},
    {id:2,name:'Paket Bulanan',duration:'1 Bulan',price:300000,description:'Akses penuh 1 bulan termasuk kelas grup.',benefits:['Akses area utama','Kelas grup','Loker gratis'],display_order:2,is_active:true,featured:true},
    {id:3,name:'Paket Kuartalan',duration:'3 Bulan',price:750000,description:'Hemat 17% dibanding paket bulanan.',benefits:['Akses area utama','Kelas grup','Loker gratis','Analisis komposisi tubuh'],display_order:3,is_active:true,featured:false},
    {id:4,name:'Paket Tahunan',duration:'12 Bulan',price:2500000,description:'Paket terbaik untuk komitmen jangka panjang.',benefits:['Akses area utama','Semua kelas grup','Loker gratis','Kaos gym gratis'],display_order:4,is_active:true,featured:false}
  ],
  galleries:[
    {id:1,title:'Area Utama',image:'img/gallery1.png',caption:'Ruang latihan utama',category:'Ruang Utama',display_order:1,is_active:true},
    {id:2,title:'Squat Rack',image:'img/gallery2.png',caption:'Area beban bebas',category:'Ruang Utama',display_order:2,is_active:true},
    {id:3,title:'Cardio Zone',image:'img/gallery3.png',caption:'Zona kardio & treadmill',category:'Kardio',display_order:3,is_active:true},
    {id:4,title:'Locker Room',image:'img/gallery4.png',caption:'Ruang ganti bersih',category:'Fasilitas',display_order:4,is_active:true},
    {id:5,title:'Kamar Mandi',image:'img/gallery5.png',caption:'Fasilitas mandi',category:'Fasilitas',display_order:5,is_active:true}
  ],
  testimonials:[
    {id:1,member_name:'Ahmad Fauzi',photo:'',quote:'Bergabung sudah 6 bulan, badan jauh lebih fit dan sehat! Sangat profesional. Highly recommended!',rating:5,is_active:true},
    {id:2,member_name:'Dewi Rahayu',photo:'',quote:'Fasilitas lengkap dan bersih. Saya sudah turun 12 kg dalam 4 bulan. Terima kasih PowerFit!',rating:5,is_active:true},
    {id:3,member_name:'Rizky Pratama',photo:'',quote:'Tempat gym terbaik yang pernah saya coba. Suasananya sangat nyaman.',rating:4,is_active:true}
  ]
};

// ===== DASHBOARD =====
function loadDashboard(){
  const pkgs=loadData('packages')||defaults.packages;
  const gals=loadData('galleries')||defaults.galleries;
  const members=JSON.parse(localStorage.getItem('wg_members')||'[]');
  const msgs=JSON.parse(localStorage.getItem('wg_messages')||'[]');
  const s=(id,v)=>{const e=document.getElementById(id);if(e)e.textContent=v;};
  s('dash-pkg',pkgs.filter(p=>p.is_active).length);
  s('dash-gal',gals.filter(g=>g.is_active).length);
  s('dash-member',members.length);
  const aktif=members.filter(m=>{
    if(m.status==='suspended')return false;
    if(!m.end_date)return true;
    return new Date(m.end_date)>=new Date();
  }).length;
  s('dash-aktif',aktif);
}

// ===== PROFIL =====
function loadProfilEditor(){
  const d=getData('profil');
  const m={gym_name:'p-name',tagline:'p-tagline',history:'p-history',vision:'p-vision',mission:'p-mission',address:'p-address',phone:'p-phone',email:'p-email',whatsapp:'p-wa',maps_link:'p-maps-link',maps_embed:'p-maps-embed'};
  Object.keys(m).forEach(k=>{const el=document.getElementById(m[k]);if(el)el.value=d[k]||'';});
}
function saveProfil(){
  saveData('profil',{gym_name:v('p-name'),tagline:v('p-tagline'),history:v('p-history'),vision:v('p-vision'),mission:v('p-mission'),address:v('p-address'),phone:v('p-phone'),email:v('p-email'),whatsapp:v('p-wa'),maps_link:v('p-maps-link'),maps_embed:v('p-maps-embed')});
}

// ===== PAKET =====
function loadPaketEditor(){
  const data=getData('packages');
  const list=document.getElementById('paketList');if(!list)return;
  list.innerHTML=data.map((p,i)=>`
    <div class="pkg-editor-item" data-idx="${i}">
      <div class="pkg-editor-header">
        <span class="pkg-editor-name">${p.name}</span>
        <div style="display:flex;gap:8px;align-items:center">
          <label style="display:flex;align-items:center;gap:6px;font-size:.8rem"><input type="checkbox" ${p.is_active?'checked':''} onchange="togglePaketActive(${i},this.checked)"> Aktif</label>
          <label style="display:flex;align-items:center;gap:6px;font-size:.8rem"><input type="checkbox" ${p.featured?'checked':''} onchange="togglePaketFeatured(${i},this.checked)"> Populer</label>
          <button onclick="deletePaket(${i})" style="color:#f55;font-size:.75rem;padding:3px 8px;border:1px solid #f55;border-radius:4px">Hapus</button>
        </div>
      </div>
      <div class="form-row cols-3">
        <div class="form-group"><label>Nama Paket</label><input type="text" id="pk-name-${i}" value="${p.name}"></div>
        <div class="form-group"><label>Durasi</label><input type="text" id="pk-dur-${i}" value="${p.duration}" placeholder="1 Bulan"></div>
        <div class="form-group"><label>Harga (angka)</label><input type="number" id="pk-price-${i}" value="${p.price}"></div>
      </div>
      <div class="form-row"><div class="form-group"><label>Deskripsi Singkat</label><input type="text" id="pk-desc-${i}" value="${p.description||''}"></div></div>
      <div class="form-row"><div class="form-group"><label>Keuntungan (satu per baris)</label><textarea id="pk-benefits-${i}" style="min-height:90px">${(Array.isArray(p.benefits)?p.benefits:[]).join('\n')}</textarea></div></div>
    </div>`).join('');
}
let _paketData=null;
function addPaket(){
  const data=getData('packages');
  data.push({id:Date.now(),name:'Paket Baru',duration:'1 Bulan',price:0,description:'',benefits:[],display_order:data.length+1,is_active:true,featured:false});
  saveData('packages',data);loadPaketEditor();
}
function deletePaket(i){
  if(!confirm('Hapus paket ini?'))return;
  const data=getData('packages');data.splice(i,1);saveData('packages',data);loadPaketEditor();
}
function togglePaketActive(i,v){const data=getData('packages');if(data[i])data[i].is_active=v;_paketData=data;}
function togglePaketFeatured(i,v){const data=getData('packages');if(data[i])data[i].featured=v;_paketData=data;}
function savePaket(){
  const data=getData('packages');
  data.forEach((p,i)=>{
    p.name=v('pk-name-'+i)||p.name;p.duration=v('pk-dur-'+i)||p.duration;
    p.price=parseFloat(document.getElementById('pk-price-'+i).value)||0;
    p.description=v('pk-desc-'+i);
    p.benefits=v('pk-benefits-'+i).split('\n').filter(l=>l.trim());
    p.display_order=i+1;
  });
  saveData('packages',data);loadPaketEditor();
}

// ===== GALERI =====
function loadGaleriEditor(){
  const data=getData('galleries');
  const list=document.getElementById('galeriList');if(!list)return;
  list.innerHTML=data.map((g,i)=>`
    <div class="gal-editor-item">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
        <strong style="font-size:.85rem">${g.title||'Foto '+(i+1)}</strong>
        <div style="display:flex;gap:8px">
          <label style="display:flex;align-items:center;gap:4px;font-size:.8rem"><input type="checkbox" ${g.is_active?'checked':''} onchange="toggleGalActive(${i},this.checked)"> Tampil</label>
          <button onclick="deleteGal(${i})" style="color:#f55;font-size:.75rem;padding:3px 8px;border:1px solid #f55;border-radius:4px">Hapus</button>
        </div>
      </div>
      <div class="form-row cols-2">
        <div class="form-group"><label>Judul</label><input type="text" id="g-title-${i}" value="${g.title||''}"></div>
        <div class="form-group"><label>Kategori</label><input type="text" id="g-cat-${i}" value="${g.category||''}" placeholder="Ruang Utama"></div>
      </div>
      <div class="form-row"><div class="form-group"><label>Path/URL Gambar</label><input type="text" id="g-img-${i}" value="${g.image||''}"></div></div>
      <div class="form-row"><div class="form-group"><label>Caption</label><input type="text" id="g-cap-${i}" value="${g.caption||''}"></div></div>
    </div>`).join('');
}
function addGaleri(){const data=getData('galleries');data.push({id:Date.now(),title:'',image:'',caption:'',category:'',display_order:data.length+1,is_active:true});saveData('galleries',data);loadGaleriEditor();}
function deleteGal(i){if(!confirm('Hapus foto ini?'))return;const data=getData('galleries');data.splice(i,1);saveData('galleries',data);loadGaleriEditor();}
function toggleGalActive(i,val){const data=getData('galleries');if(data[i])data[i].is_active=val;}
function saveGaleri(){
  const data=getData('galleries');
  data.forEach((g,i)=>{g.title=v('g-title-'+i);g.category=v('g-cat-'+i);g.image=v('g-img-'+i);g.caption=v('g-cap-'+i);g.display_order=i+1;});
  saveData('galleries',data);loadGaleriEditor();
}

// ===== TESTIMONI =====
function loadTestimoniEditor(){
  const data=getData('testimonials');
  const list=document.getElementById('testimoniList');if(!list)return;
  list.innerHTML=data.map((t,i)=>`
    <div class="testi-editor-item">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <strong>${t.member_name||'Testimoni '+(i+1)}</strong>
        <div style="display:flex;gap:8px">
          <label style="display:flex;align-items:center;gap:4px;font-size:.8rem"><input type="checkbox" ${t.is_active?'checked':''} onchange="toggleTestiActive(${i},this.checked)"> Tampil</label>
          <button onclick="deleteTesti(${i})" style="color:#f55;font-size:.75rem;padding:3px 8px;border:1px solid #f55;border-radius:4px">Hapus</button>
        </div>
      </div>
      <div class="form-row cols-2"><div class="form-group"><label>Nama Member</label><input type="text" id="t-name-${i}" value="${t.member_name||''}"></div><div class="form-group"><label>Rating (1-5)</label><input type="number" id="t-rating-${i}" value="${t.rating||5}" min="1" max="5"></div></div>
      <div class="form-row"><div class="form-group"><label>URL Foto (opsional)</label><input type="text" id="t-photo-${i}" value="${t.photo||''}" placeholder="img/member.jpg"></div></div>
      <div class="form-row"><div class="form-group"><label>Isi Testimoni</label><textarea id="t-quote-${i}" style="min-height:80px">${t.quote||''}</textarea></div></div>
    </div>`).join('');
}
function addTestimoni(){const data=getData('testimonials');data.push({id:Date.now(),member_name:'',photo:'',quote:'',rating:5,is_active:true});saveData('testimonials',data);loadTestimoniEditor();}
function deleteTesti(i){if(!confirm('Hapus testimoni ini?'))return;const data=getData('testimonials');data.splice(i,1);saveData('testimonials',data);loadTestimoniEditor();}
function toggleTestiActive(i,val){const data=getData('testimonials');if(data[i])data[i].is_active=val;}
function saveTestimoni(){
  const data=getData('testimonials');
  data.forEach((t,i)=>{t.member_name=v('t-name-'+i);t.rating=parseInt(document.getElementById('t-rating-'+i).value)||5;t.photo=v('t-photo-'+i);t.quote=v('t-quote-'+i);});
  saveData('testimonials',data);loadTestimoniEditor();
}

// ===== PESAN MASUK =====
function loadPesan(){
  const msgs=JSON.parse(localStorage.getItem('wg_messages')||'[]');
  const list=document.getElementById('pesanList');if(!list)return;
  if(!msgs.length){list.innerHTML='<p style="color:#666;text-align:center;padding:40px">Belum ada pesan masuk.</p>';return;}
  list.innerHTML=msgs.slice().reverse().map((m,i)=>`
    <div class="pesan-item${m.is_read?'':' unread'}" id="pesan-${m.id}">
      <div class="pesan-header">
        <div><strong>${m.name}</strong> <span style="font-size:.78rem;color:#888">&lt;${m.email}&gt;</span>${m.phone?` · ${m.phone}`:''}</div>
        <div style="display:flex;gap:8px;align-items:center">
          <span style="font-size:.72rem;color:#666">${new Date(m.created_at).toLocaleString('id-ID')}</span>
          ${!m.is_read?`<button onclick="markRead('${m.id}')" style="font-size:.72rem;padding:3px 10px;background:var(--color-accent);color:#000;border-radius:4px;font-weight:700">Tandai Dibaca</button>`:'<span style="font-size:.72rem;color:#4CAF50">✓ Dibaca</span>'}
        </div>
      </div>
      <p class="pesan-body">${m.message}</p>
    </div>`).join('');
}
function markRead(id){
  const msgs=JSON.parse(localStorage.getItem('wg_messages')||'[]');
  const m=msgs.find(x=>x.id==id);if(m)m.is_read=true;
  localStorage.setItem('wg_messages',JSON.stringify(msgs));loadPesan();loadDashboard();
}

// ===== AKUN =====
async function changePassword(){
  const nu=v('new-username'),op=v('old-password'),np=v('new-password'),cp=v('confirm-password');
  if(np&&np!==cp){showToast('Konfirmasi password tidak cocok!','error');return;}
  if(np&&np.length<6){showToast('Password minimal 6 karakter!','error');return;}
  const stored=loadData('admin_creds')||{username:'admin',hash:'$wg$warriors2026'};
  const oldHash=await sha256(op);
  if(op&&stored.hash!=='$wg$warriors2026'&&stored.hash!==oldHash){showToast('Password lama salah!','error');return;}
  const newHash=np?await sha256(np):stored.hash;
  saveData('admin_creds',{username:nu||stored.username,hash:newHash});
  showToast('Kredensial berhasil diperbarui!');
}

// ===== DATA MEMBER =====
function getMembers(){return loadData('members')||[];}
function saveMembers(data){localStorage.setItem('wg_members',JSON.stringify(data));showToast('Data member disimpan.');}
function getMemberStatus(m){
  if(m.status==='suspended')return 'suspended';
  if(!m.end_date)return m.status||'active';
  return new Date(m.end_date)<new Date()?'expired':'active';
}
function fmtDate(d){if(!d)return '-';const dt=new Date(d);return dt.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});}
function statusBadge(s){const map={active:'<span class="mbadge mbadge-active">Aktif</span>',expired:'<span class="mbadge mbadge-expired">Kadaluarsa</span>',suspended:'<span class="mbadge mbadge-suspended">Ditangguhkan</span>'};return map[s]||map.active;}

function loadMemberPanel(){
  const pkgs=getData('packages').filter(p=>p.is_active);
  const sel=document.getElementById('mf-package');
  if(sel)sel.innerHTML=pkgs.map(p=>`<option value="${p.name}">${p.name}</option>`).join('');
  renderMemberTable();
}
function renderMemberTable(){
  const members=getMembers();
  const q=(document.getElementById('memberSearch')||{}).value||'';
  const sf=(document.getElementById('memberStatusFilter')||{}).value||'';
  const body=document.getElementById('memberTableBody');
  const empty=document.getElementById('memberEmpty');
  if(!body)return;
  const filtered=members.filter(m=>{
    const status=getMemberStatus(m);
    const matchQ=!q||(m.name||'').toLowerCase().includes(q.toLowerCase())||(m.email||'').toLowerCase().includes(q.toLowerCase());
    const matchS=!sf||status===sf;
    return matchQ&&matchS;
  });
  if(!filtered.length){body.innerHTML='';if(empty)empty.style.display='block';return;}
  if(empty)empty.style.display='none';
  body.innerHTML=filtered.map(m=>{
    const status=getMemberStatus(m);
    return `<tr>
      <td><strong>${m.name||'-'}</strong></td>
      <td>${m.email||'-'}</td>
      <td>${m.phone||'-'}</td>
      <td>${m.package_name||'-'}</td>
      <td>${fmtDate(m.start_date)}</td>
      <td>${fmtDate(m.end_date)}</td>
      <td>${statusBadge(status)}</td>
      <td class="member-actions">
        <button onclick="editMember('${m.id}')" class="btn-edit-member">Edit</button>
        <button onclick="deleteMember('${m.id}')" class="btn-del-member">Hapus</button>
      </td>
    </tr>`;
  }).join('');
}
function showMemberForm(clear){
  document.getElementById('memberFormWrap').style.display='block';
  document.getElementById('memberFormTitle').textContent='Tambah Member Baru';
  if(clear!==false){['mf-id','mf-name','mf-email','mf-phone','mf-notes','mf-start','mf-end'].forEach(id=>{const e=document.getElementById(id);if(e)e.value='';});
  document.getElementById('mf-status').value='active';}
}
function hideMemberForm(){document.getElementById('memberFormWrap').style.display='none';}
function saveMember(){
  const members=getMembers();
  const id=v('mf-id');
  const entry={
    id:id||String(Date.now()),
    name:v('mf-name'),email:v('mf-email'),phone:v('mf-phone'),
    package_name:v('mf-package'),start_date:v('mf-start'),end_date:v('mf-end'),
    status:v('mf-status'),notes:v('mf-notes'),
    created_at:id?undefined:new Date().toISOString()
  };
  if(!entry.name){showToast('Nama member tidak boleh kosong!','error');return;}
  if(id){const idx=members.findIndex(m=>m.id===id);if(idx>-1)members[idx]={...members[idx],...entry};}
  else{members.push(entry);}
  saveMembers(members);hideMemberForm();renderMemberTable();loadDashboard();
}
function editMember(id){
  const m=getMembers().find(x=>x.id===id);if(!m)return;
  showMemberForm(false);
  document.getElementById('memberFormTitle').textContent='Edit Member';
  const set=(fid,val)=>{const e=document.getElementById(fid);if(e)e.value=val||'';};
  set('mf-id',m.id);set('mf-name',m.name);set('mf-email',m.email);set('mf-phone',m.phone);
  set('mf-package',m.package_name);set('mf-start',m.start_date);set('mf-end',m.end_date);
  set('mf-status',getMemberStatus(m));set('mf-notes',m.notes);
}
function deleteMember(id){
  if(!confirm('Hapus data member ini?'))return;
  const members=getMembers().filter(m=>m.id!==id);
  saveMembers(members);renderMemberTable();loadDashboard();
}
