import{_ as s,o as a,c as l,Q as n}from"./chunks/framework.a7175731.js";const h=JSON.parse('{"title":"Encrypted images","description":"","frontmatter":{},"headers":[],"relativePath":"Downloading - displaying Images.md","filePath":"Downloading - displaying Images.md"}'),o={name:"Downloading - displaying Images.md"},p=n(`<h1 id="encrypted-images" tabindex="-1">Encrypted images <a class="header-anchor" href="#encrypted-images" aria-label="Permalink to &quot;Encrypted images&quot;">​</a></h1><h2 id="downloading-the-image" tabindex="-1">Downloading the image <a class="header-anchor" href="#downloading-the-image" aria-label="Permalink to &quot;Downloading the image&quot;">​</a></h2><p>In your controller</p><div class="language-php vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">php</span><pre class="shiki github-dark vp-code-dark"><code><span class="line"><span style="color:#E1E4E8;">    $file </span><span style="color:#F97583;">=</span><span style="color:#E1E4E8;"> </span><span style="color:#79B8FF;">MemberFile</span><span style="color:#F97583;">::</span><span style="color:#B392F0;">where</span><span style="color:#E1E4E8;">(</span><span style="color:#9ECBFF;">&#39;xid&#39;</span><span style="color:#E1E4E8;">, $xid)</span><span style="color:#F97583;">-&gt;</span><span style="color:#B392F0;">firstOrFail</span><span style="color:#E1E4E8;">();</span></span>
<span class="line"><span style="color:#E1E4E8;">    </span><span style="color:#F97583;">return</span><span style="color:#E1E4E8;">  </span><span style="color:#79B8FF;">FileServe</span><span style="color:#F97583;">::</span><span style="color:#B392F0;">download</span><span style="color:#E1E4E8;">($file</span><span style="color:#F97583;">-&gt;</span><span style="color:#E1E4E8;">disk, $file</span><span style="color:#F97583;">-&gt;</span><span style="color:#E1E4E8;">fullPath);</span></span></code></pre><pre class="shiki github-light vp-code-light"><code><span class="line"><span style="color:#24292E;">    $file </span><span style="color:#D73A49;">=</span><span style="color:#24292E;"> </span><span style="color:#005CC5;">MemberFile</span><span style="color:#D73A49;">::</span><span style="color:#6F42C1;">where</span><span style="color:#24292E;">(</span><span style="color:#032F62;">&#39;xid&#39;</span><span style="color:#24292E;">, $xid)</span><span style="color:#D73A49;">-&gt;</span><span style="color:#6F42C1;">firstOrFail</span><span style="color:#24292E;">();</span></span>
<span class="line"><span style="color:#24292E;">    </span><span style="color:#D73A49;">return</span><span style="color:#24292E;">  </span><span style="color:#005CC5;">FileServe</span><span style="color:#D73A49;">::</span><span style="color:#6F42C1;">download</span><span style="color:#24292E;">($file</span><span style="color:#D73A49;">-&gt;</span><span style="color:#24292E;">disk, $file</span><span style="color:#D73A49;">-&gt;</span><span style="color:#24292E;">fullPath);</span></span></code></pre></div><h2 id="displaying-image" tabindex="-1">Displaying image <a class="header-anchor" href="#displaying-image" aria-label="Permalink to &quot;Displaying image&quot;">​</a></h2><h3 id="retrieve-the-image" tabindex="-1">Retrieve the image <a class="header-anchor" href="#retrieve-the-image" aria-label="Permalink to &quot;Retrieve the image&quot;">​</a></h3><div class="language-php vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">php</span><pre class="shiki github-dark vp-code-dark"><code><span class="line"><span style="color:#E1E4E8;">    $file </span><span style="color:#F97583;">=</span><span style="color:#E1E4E8;"> </span><span style="color:#79B8FF;">MemberFile</span><span style="color:#F97583;">::</span><span style="color:#B392F0;">where</span><span style="color:#E1E4E8;">(</span><span style="color:#9ECBFF;">&#39;xid&#39;</span><span style="color:#E1E4E8;">, $xid)</span><span style="color:#F97583;">-&gt;</span><span style="color:#B392F0;">firstOrFail</span><span style="color:#E1E4E8;">();</span></span>
<span class="line"><span style="color:#E1E4E8;">    $imagedata </span><span style="color:#F97583;">=</span><span style="color:#E1E4E8;"> </span><span style="color:#79B8FF;">FileServe</span><span style="color:#F97583;">::</span><span style="color:#B392F0;">view</span><span style="color:#E1E4E8;">($file</span><span style="color:#F97583;">-&gt;</span><span style="color:#E1E4E8;">disk, $file</span><span style="color:#F97583;">-&gt;</span><span style="color:#E1E4E8;">fullPath, $file</span><span style="color:#F97583;">-&gt;</span><span style="color:#E1E4E8;">mime); </span><span style="color:#6A737D;">// ie data:image/png;base64,XXXX</span></span></code></pre><pre class="shiki github-light vp-code-light"><code><span class="line"><span style="color:#24292E;">    $file </span><span style="color:#D73A49;">=</span><span style="color:#24292E;"> </span><span style="color:#005CC5;">MemberFile</span><span style="color:#D73A49;">::</span><span style="color:#6F42C1;">where</span><span style="color:#24292E;">(</span><span style="color:#032F62;">&#39;xid&#39;</span><span style="color:#24292E;">, $xid)</span><span style="color:#D73A49;">-&gt;</span><span style="color:#6F42C1;">firstOrFail</span><span style="color:#24292E;">();</span></span>
<span class="line"><span style="color:#24292E;">    $imagedata </span><span style="color:#D73A49;">=</span><span style="color:#24292E;"> </span><span style="color:#005CC5;">FileServe</span><span style="color:#D73A49;">::</span><span style="color:#6F42C1;">view</span><span style="color:#24292E;">($file</span><span style="color:#D73A49;">-&gt;</span><span style="color:#24292E;">disk, $file</span><span style="color:#D73A49;">-&gt;</span><span style="color:#24292E;">fullPath, $file</span><span style="color:#D73A49;">-&gt;</span><span style="color:#24292E;">mime); </span><span style="color:#6A737D;">// ie data:image/png;base64,XXXX</span></span></code></pre></div><h3 id="return-the-imagedata-to-the-view" tabindex="-1">Return the imagedata to the view <a class="header-anchor" href="#return-the-imagedata-to-the-view" aria-label="Permalink to &quot;Return the imagedata to the view&quot;">​</a></h3><p>Display with:</p><div class="language-html vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">html</span><pre class="shiki github-dark vp-code-dark"><code><span class="line"><span style="color:#E1E4E8;">&lt;</span><span style="color:#85E89D;">img</span><span style="color:#E1E4E8;"> </span><span style="color:#B392F0;">src</span><span style="color:#E1E4E8;">=</span><span style="color:#9ECBFF;">&quot;{{imagedata}}&quot;</span><span style="color:#E1E4E8;">&gt;</span></span></code></pre><pre class="shiki github-light vp-code-light"><code><span class="line"><span style="color:#24292E;">&lt;</span><span style="color:#22863A;">img</span><span style="color:#24292E;"> </span><span style="color:#6F42C1;">src</span><span style="color:#24292E;">=</span><span style="color:#032F62;">&quot;{{imagedata}}&quot;</span><span style="color:#24292E;">&gt;</span></span></code></pre></div><h2 id="display-pdf" tabindex="-1">Display pdf <a class="header-anchor" href="#display-pdf" aria-label="Permalink to &quot;Display pdf&quot;">​</a></h2><div class="language-html vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">html</span><pre class="shiki github-dark vp-code-dark"><code><span class="line"><span style="color:#E1E4E8;">  &lt;</span><span style="color:#85E89D;">img</span><span style="color:#E1E4E8;"> </span><span style="color:#B392F0;">:src</span><span style="color:#E1E4E8;">=</span><span style="color:#9ECBFF;">&quot;fromEncrypted&quot;</span><span style="color:#E1E4E8;">&gt;</span></span>
<span class="line"><span style="color:#E1E4E8;">  &lt;</span><span style="color:#85E89D;">h1</span><span style="color:#E1E4E8;">&gt; igrame&lt;/</span><span style="color:#85E89D;">h1</span><span style="color:#E1E4E8;">&gt;</span></span>
<span class="line"><span style="color:#E1E4E8;">  &lt;</span><span style="color:#85E89D;">iframe</span><span style="color:#E1E4E8;"> </span><span style="color:#B392F0;">:src</span><span style="color:#E1E4E8;">=</span><span style="color:#9ECBFF;">&quot;fromEncrypted&quot;</span><span style="color:#FDAEB7;font-style:italic;">/</span><span style="color:#E1E4E8;">&gt;</span></span>
<span class="line"><span style="color:#E1E4E8;">    &lt;</span><span style="color:#85E89D;">h1</span><span style="color:#E1E4E8;">&gt;Object&lt;/</span><span style="color:#85E89D;">h1</span><span style="color:#E1E4E8;">&gt;</span></span>
<span class="line"><span style="color:#E1E4E8;">    &lt;</span><span style="color:#85E89D;">object</span><span style="color:#E1E4E8;"> </span><span style="color:#B392F0;">:data</span><span style="color:#E1E4E8;">=</span><span style="color:#9ECBFF;">&quot;fromEncrypted&quot;</span><span style="color:#FDAEB7;font-style:italic;">/</span><span style="color:#E1E4E8;">&gt;</span></span>
<span class="line"><span style="color:#E1E4E8;">&lt;</span><span style="color:#85E89D;">object</span><span style="color:#E1E4E8;"> </span><span style="color:#B392F0;">:data</span><span style="color:#E1E4E8;">=</span><span style="color:#9ECBFF;">&quot;fromEncrypted&quot;</span><span style="color:#E1E4E8;"> </span><span style="color:#B392F0;">width</span><span style="color:#E1E4E8;">=</span><span style="color:#9ECBFF;">&quot;560&quot;</span><span style="color:#E1E4E8;"> </span><span style="color:#B392F0;">height</span><span style="color:#E1E4E8;">=</span><span style="color:#9ECBFF;">&quot;615&quot;</span><span style="color:#FDAEB7;font-style:italic;">/</span><span style="color:#E1E4E8;">&gt;</span></span></code></pre><pre class="shiki github-light vp-code-light"><code><span class="line"><span style="color:#24292E;">  &lt;</span><span style="color:#22863A;">img</span><span style="color:#24292E;"> </span><span style="color:#6F42C1;">:src</span><span style="color:#24292E;">=</span><span style="color:#032F62;">&quot;fromEncrypted&quot;</span><span style="color:#24292E;">&gt;</span></span>
<span class="line"><span style="color:#24292E;">  &lt;</span><span style="color:#22863A;">h1</span><span style="color:#24292E;">&gt; igrame&lt;/</span><span style="color:#22863A;">h1</span><span style="color:#24292E;">&gt;</span></span>
<span class="line"><span style="color:#24292E;">  &lt;</span><span style="color:#22863A;">iframe</span><span style="color:#24292E;"> </span><span style="color:#6F42C1;">:src</span><span style="color:#24292E;">=</span><span style="color:#032F62;">&quot;fromEncrypted&quot;</span><span style="color:#B31D28;font-style:italic;">/</span><span style="color:#24292E;">&gt;</span></span>
<span class="line"><span style="color:#24292E;">    &lt;</span><span style="color:#22863A;">h1</span><span style="color:#24292E;">&gt;Object&lt;/</span><span style="color:#22863A;">h1</span><span style="color:#24292E;">&gt;</span></span>
<span class="line"><span style="color:#24292E;">    &lt;</span><span style="color:#22863A;">object</span><span style="color:#24292E;"> </span><span style="color:#6F42C1;">:data</span><span style="color:#24292E;">=</span><span style="color:#032F62;">&quot;fromEncrypted&quot;</span><span style="color:#B31D28;font-style:italic;">/</span><span style="color:#24292E;">&gt;</span></span>
<span class="line"><span style="color:#24292E;">&lt;</span><span style="color:#22863A;">object</span><span style="color:#24292E;"> </span><span style="color:#6F42C1;">:data</span><span style="color:#24292E;">=</span><span style="color:#032F62;">&quot;fromEncrypted&quot;</span><span style="color:#24292E;"> </span><span style="color:#6F42C1;">width</span><span style="color:#24292E;">=</span><span style="color:#032F62;">&quot;560&quot;</span><span style="color:#24292E;"> </span><span style="color:#6F42C1;">height</span><span style="color:#24292E;">=</span><span style="color:#032F62;">&quot;615&quot;</span><span style="color:#B31D28;font-style:italic;">/</span><span style="color:#24292E;">&gt;</span></span></code></pre></div>`,12),e=[p];function t(r,c,y,i,E,d){return a(),l("div",null,e)}const F=s(o,[["render",t]]);export{h as __pageData,F as default};