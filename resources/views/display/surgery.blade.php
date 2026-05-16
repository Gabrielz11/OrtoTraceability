<x-display.layouts.kiosk>
    <div x-data="surgeryDisplay({{ $surgery->id }}, {{ $surgery->materials->toJson() }})" class="h-full grid grid-cols-12 gap-8">
        
        <!-- Left Column: Surgery Info & Materials (2/3) -->
        <div class="col-span-8 flex flex-col space-y-8">
            
            <!-- Surgery Header Card -->
            <div class="bg-slate-800/50 p-6 border-l-4 border-emerald-500 rounded-r-xl shadow-2xl">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-slate-400 text-sm font-bold uppercase tracking-widest font-mono-tech mb-1">Live Procedure Display</h2>
                        <h1 class="text-3xl font-bold text-white tracking-tight">Paciente: {{ $surgery->paciente }}</h1>
                        <p class="text-slate-400 text-lg mt-1">{{ $surgery->medico }} | {{ $surgery->hospital }}</p>
                    </div>
                    <div class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded text-emerald-500 font-bold font-mono-tech text-xl">
                        ID-{{ str_pad($surgery->id, 4, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            </div>

            <!-- Divergence Alerts (DYNAMIC) -->
            <template x-for="alert in alerts" :key="alert.id">
                <div class="bg-red-950 border-2 border-red-500 p-6 rounded-xl animate-bounce shadow-[0_0_20px_rgba(239,68,68,0.4)]">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-red-500 rounded-full">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 1.333c1.54 0 2.502-1.667 1.732-3L13.732 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-red-500 uppercase">DIVERGÊNCIA CRÍTICA DETECTADA</h3>
                            <p class="text-xl text-red-100" x-text="alert.message"></p>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Materials Table -->
            <div class="flex-grow flex flex-col bg-slate-800/30 rounded-xl border border-slate-700/50 overflow-hidden">
                <div class="px-6 py-4 bg-slate-800/80 border-b border-slate-700 flex justify-between items-center text-xs font-bold uppercase tracking-widest text-slate-400 font-mono-tech">
                    <span>Materials Traceability Chain</span>
                    <span x-text="`${materials.length} items total`"></span>
                </div>
                <div class="flex-grow overflow-y-auto">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-slate-900 text-xs text-slate-500 uppercase font-black">
                            <tr>
                                <th class="px-6 py-3">Material / Código</th>
                                <th class="px-6 py-3">Batch/Lote</th>
                                <th class="px-6 py-3 text-center">Security Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <template x-for="material in materials" :key="material.id">
                                <tr class="hover:bg-slate-700/20 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-xl text-white" x-text="material.nome"></div>
                                        <div class="text-sm text-slate-500" x-text="material.numero_serie || 'NO-SERIAL'"></div>
                                    </td>
                                    <td class="px-6 py-5 font-mono text-lg text-slate-400" x-text="material.lote"></td>
                                    <td class="px-6 py-5">
                                        <div class="flex justify-center">
                                            <span :class="{
                                                'px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest': true,
                                                'bg-emerald-500/20 text-emerald-400 border border-emerald-500/50': material.pivot.acao === 'usado',
                                                'bg-sky-500/20 text-sky-400 border border-sky-500/50': material.pivot.acao === 'reservado'
                                            }" x-text="material.pivot.acao === 'usado' ? '✅ Implemented' : '🔵 Allocated'"></span>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="materials.length === 0">
                                <tr>
                                    <td colspan="3" class="px-6 py-20 text-center text-slate-600 font-mono-tech italic">
                                        Awaiting material allocation protocol...
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Live Feed (1/3) -->
        <div class="col-span-4 flex flex-col bg-black/20 rounded-xl border border-slate-700/50 overflow-hidden shadow-inner">
            <div class="px-6 py-4 bg-slate-800/80 border-b border-slate-700 flex items-center space-x-3">
                <div class="w-2 h-2 bg-red-500 rounded-full animate-ping"></div>
                <span class="text-xs font-bold uppercase tracking-widest text-white font-mono-tech">Internal Process Log</span>
            </div>
            
            <div class="flex-grow p-6 overflow-y-auto space-y-6 flex flex-col-reverse justify-end">
                <template x-for="(entry, index) in timeline" :key="index">
                    <div class="flex space-x-4 animate-fadeIn">
                        <div class="flex flex-col items-center">
                            <div class="w-2 h-2 rounded-full border border-slate-500" :class="entry.type.includes('divergence') ? 'bg-red-500' : 'bg-slate-400'"></div>
                            <div class="w-px flex-grow bg-slate-800"></div>
                        </div>
                        <div class="pb-1">
                            <div class="text-[10px] text-slate-500 font-mono-tech mb-1" x-text="formatTime(entry.occurred_at)"></div>
                            <div :class="{
                                'text-sm font-medium leading-tight': true,
                                'text-red-400': entry.type.includes('divergence'),
                                'text-slate-300': !entry.type.includes('divergence')
                            }">
                                <span class="font-bold text-slate-100" x-text="entry.actor_role"></span>
                                <span x-text="entry.message"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div class="text-center text-[10px] text-slate-700 font-mono-tech mb-4 uppercase">
                    --- Session Handshake Established ---
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('surgeryDisplay', (surgeryId, initialMaterials) => ({
                materials: initialMaterials,
                timeline: [],
                alerts: [],
                
                init() {
                    console.log('--- Display initialized for Surgery #' + surgeryId + ' ---');
                    console.log('Connecting to channel: surgery.' + surgeryId);
                    
                    // Connect to the Public Channel surgery.{id}
                    window.Echo.channel('surgery.' + surgeryId)
                        .subscribed(() => {
                            console.log('✅ Subscribed to channel surgery.' + surgeryId);
                        })
                        .listen('.surgery.material_linked', (e) => {
                            console.log('🔔 Event Received: material_linked', e);
                            this.addTimelineEntry(e, 'vinculou o material ' + e.material_name);
                            this.refreshMaterials(surgeryId);
                        })
                        .listen('.surgery.material_unlinked', (e) => {
                            console.log('🔔 Event Received: material_unlinked', e);
                            this.addTimelineEntry(e, 'desvinculou o material ' + e.material_name);
                            this.refreshMaterials(surgeryId);
                        })
                        .listen('.material.used', (e) => {
                            console.log('🔔 Event Received: material_used', e);
                            this.addTimelineEntry(e, 'marcou como USADO o material ' + e.material_name);
                            this.refreshMaterials(surgeryId);
                        })
                        .listen('.material.divergence_detected', (e) => {
                            console.log('🚨 Event Received: DIVERGENCE', e);
                            this.addAlert(e);
                        });
                },

                async refreshMaterials(id) {
                    // Simple polling for a specific data update when an event happens
                    // In a highly optimized version, we'd update the local array directly from broadcast data
                    const response = await fetch(`/api/surgeries/${id}/materials-status`);
                    const data = await response.json();
                    this.materials = data.materials;
                },

                addTimelineEntry(e, message) {
                    this.timeline.push({
                        type: e.type,
                        message: message,
                        occurred_at: e.occurred_at,
                        actor_role: (e.actor_role || 'SYSTEM').toUpperCase() + ':'
                    });
                },

                addAlert(e) {
                    const message = e.divergences[0]?.message || 'Erro de validação detectado.';
                    const alert = { id: Date.now(), message: message };
                    this.alerts.push(alert);
                    
                    this.addTimelineEntry({
                        type: 'divergence',
                        occurred_at: e.occurred_at,
                        actor_role: 'SECURITY ENGINE'
                    }, '⚠️ Divergência: ' + message);

                    // Auto-remove alert after 15 seconds
                    setTimeout(() => {
                        this.alerts = this.alerts.filter(a => a.id !== alert.id);
                    }, 15000);
                },

                formatTime(isoString) {
                    const date = new Date(isoString);
                    return date.getHours().toString().padStart(2, '0') + ':' + 
                           date.getMinutes().toString().padStart(2, '0') + ':' + 
                           date.getSeconds().toString().padStart(2, '0');
                }
            }));
        });
    </script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.4s ease-out forwards;
        }
    </style>
</x-display.layouts.kiosk>
