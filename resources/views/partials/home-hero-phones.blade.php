{{-- STAGING: hero illustration — two tall phones with Carfax-style and AutoCheck-style
     report mockups (rendered HTML/CSS). Nominative use; the site resells these reports. --}}
<div class="relative w-full max-w-md mx-auto lg:mx-0 h-[540px] select-none" aria-hidden="true">

    {{-- Phone 1 — Carfax style (back, tilted left) --}}
    <div class="absolute left-0 top-6 w-52 -rotate-[8deg] rounded-[2.2rem] bg-gray-900 p-1.5 shadow-2xl">
        <div class="relative rounded-[1.7rem] bg-white overflow-hidden min-h-[440px] flex flex-col">
            <div class="absolute top-1.5 left-1/2 -translate-x-1/2 w-12 h-1.5 bg-gray-900 rounded-full z-10"></div>
            <div class="bg-[#2f6ca5] text-white px-3 pt-5 pb-2">
                <div class="font-black tracking-tight text-sm leading-none">CARFAX</div>
                <div class="text-[7px] tracking-[0.15em] opacity-80 mt-0.5">VEHICLE HISTORY REPORT</div>
            </div>
            <div class="p-3 text-[10px] text-gray-700 flex-1">
                <div class="font-bold text-gray-900 text-sm">Audi A5</div>
                <div>Located in: <b>Poland</b></div>
                <div class="text-gray-500">VIN: WAULFAFR4EA••••••</div>
                <div class="mt-2 text-[8px] text-gray-500 leading-relaxed">
                    Model year: <b class="text-gray-700">2014</b> · Coupe · Petrol<br>Engine: 1984 cm³ · 162 kW / 220 hp
                </div>
                <div class="mt-2 space-y-1.5">
                    <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-sm bg-red-500 shrink-0"></span> Salvage title issued</div>
                    <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-sm bg-red-500 shrink-0"></span> Accident reported</div>
                    <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-sm bg-[#2f6ca5] shrink-0"></span> Imported vehicle</div>
                    <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-sm bg-[#2f6ca5] shrink-0"></span> 4 odometer readings</div>
                    <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-sm bg-[#2f6ca5] shrink-0"></span> 2 previous owners</div>
                </div>
            </div>
            <div class="bg-gray-100 px-3 py-2 text-[9px] font-semibold text-gray-500 flex items-center justify-between">
                Quick Check <span>⌃</span>
            </div>
        </div>
    </div>

    {{-- Phone 2 — AutoCheck style (front, tilted right) --}}
    <div class="absolute right-0 top-24 w-52 rotate-[6deg] rounded-[2.2rem] bg-gray-900 p-1.5 shadow-2xl z-10">
        <div class="relative rounded-[1.7rem] bg-white overflow-hidden min-h-[440px] flex flex-col">
            <div class="absolute top-1.5 left-1/2 -translate-x-1/2 w-12 h-1.5 bg-gray-900 rounded-full z-10"></div>
            <div class="bg-[#0b3d6b] text-white px-3 pt-5 pb-2 flex items-center justify-between">
                <div class="font-black text-sm leading-none">AutoCheck<span class="align-super text-[7px]">®</span></div>
                <div class="text-[7px] tracking-widest bg-white/15 px-1.5 py-0.5 rounded">SCORE</div>
            </div>
            <div class="p-3 text-[10px] text-gray-700 flex-1">
                <div class="flex items-center justify-between mb-2">
                    <div class="font-bold text-gray-900 text-sm leading-tight">Audi A5<br><span class="text-[9px] font-normal text-gray-500">2014 · Coupe</span></div>
                    <div class="w-10 h-10 rounded-full bg-[#0b3d6b] text-white flex flex-col items-center justify-center shrink-0 leading-none">
                        <span class="font-extrabold text-sm">89</span><span class="text-[6px] opacity-80">/100</span>
                    </div>
                </div>
                <div class="text-[8px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Highlights</div>
                <div class="space-y-1 mb-3">
                    <div class="flex justify-between border-b border-gray-100 pb-1"><span>Owners</span><b class="text-gray-900">2</b></div>
                    <div class="flex justify-between border-b border-gray-100 pb-1"><span>Odometer</span><b class="text-gray-900">128,315</b></div>
                    <div class="flex justify-between border-b border-gray-100 pb-1"><span>Vehicle age</span><b class="text-gray-900">8 yrs</b></div>
                    <div class="flex justify-between border-b border-gray-100 pb-1"><span>Accidents</span><b class="text-gray-900">1 reported</b></div>
                    <div class="flex justify-between"><span>Last inspection</span><b class="text-gray-900">2021-09</b></div>
                </div>
                <div class="text-[8px] font-semibold text-gray-400 uppercase tracking-wider mb-1">History</div>
                <div class="space-y-1 text-[9px]">
                    <div class="flex justify-between"><span class="text-gray-500">2014-07</span><span>First registration</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">2018-03</span><span>Service record</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">2021-09</span><span>Inspection</span></div>
                </div>
            </div>
            <div class="bg-gray-100 px-3 py-2 text-[9px] font-semibold text-gray-500 flex items-center justify-between">
                History information <span>⌃</span>
            </div>
        </div>
    </div>

</div>
