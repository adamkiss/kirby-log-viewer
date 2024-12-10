<template>
	<k-panel-inside>
		<k-header>
			<span class="inline-flex gap-2 items-baseline">
				<span>Logy: {{ log?.range }}</span>
				<time class="text-base opacity-70 align-right">{{ currtime }}</time>
			</span>


			<div slot="buttons">
				<k-input
					:options="picklist"
					:value="log?.name"
					:required="true"
					@input="onChangeLog"
					ref="input"
					type="select"
				/>
			</div>
		</k-header>

		<div v-for="([d, lines]) in logs" :key="d" class="mb-8">
			<h2 class="text-2xl mb-4">{{ d }}</h2>
			<k-input class="block w-full">
				<div class="w-full overflow-x-scroll" v-for="(line, j) in lines" :key="j">
					<pre v-html="line.content" :class="line.class"></pre>
				</div>
			</k-input>
		</div>
	</k-panel-inside>
</template>

<style scoped>
.k-input {
	line-height: 1.4;
	min-height: auto !important;
}
.k-input-element {
	display: block;
	width: 100%;
}
.k-input div:has(pre:empty) {
	display: none;
}
.k-input pre {
	padding: 1rem;
}
.k-input div:not(:last-child) pre {
	border-bottom: 1px solid var(--input-color-border);
}
</style>

<style>
.inline-flex {
	display: inline-flex;
}
.gap-2 {
	gap: 0.5rem;
}
.items-baseline {
	align-items: baseline;
}
.text-base {
	font-size: 1.125rem;
}
.opacity-70 {
	opacity: 0.7;
}
.text-2xl {
	font-size: 1.875rem;
}
.mb-4 {
	margin-bottom: 1rem;
}
.align-right {
	text-align: right;
}

[class^='sev-'] > * {
	mix-blend-mode: multiply;
}

[class^='sev-'] > time:nth-of-type(n+2) {
	color: var(--color-gray-500);
}

[class^='sev-'] .punc {
	color: var(--color-gray-500);
}
[class^='sev-'] .sev {
	font-weight: bold;
}
[class^='sev-'] .tag {
	border: 1px solid var(--color-gray-400);
	padding: 0.2rem;
	border-radius: 3px;
}

.sev.sev-debug { color: #999; }
.sev.sev-info { color: var(--color-blue-700); }
.sev.sev-notice { color: var(--color-purple-700); }
.sev.sev-warning { color: var(--color-orange-700); }
.sev.sev-error { color: var(--color-red-700); }

.sev-warning { background: var(--color-orange-200); }
.sev-error { background: var(--color-red-300); }

[hl] { color: #666; }
[hl] .hljs-attr { color: var(--color-blue-800); }
[hl] .hljs-string, [hl] .hljs-number { color: var(--color-purple-800); }
</style>

<script>
export default {
	props: {
		all: Object,
		log: Object,
		content: Object,
	},
	data() {
		return {
			currtime: '…'
		}
	},
	computed: {
		logs() {
			return Object.keys(this.content).map(k => ([
				k,
				this.content[k]
			]))
			.filter(([_, v]) => {
				if (undefined === v || !Array.isArray(v) || !v.length) {
					return false;
				}
				if (v.length === 1 && (undefined === v[0].content || '' === v[0].content)) {
					return false;
				}
				return true;
			})
		},
		picklist() {
			return Object.keys(this.all).map((k) => ({
				value: k,
				text: `${k} (${this.all[k].range})`,
			}));
		},
	},
	mounted() {
		setInterval(() => {
			this.currtime = this.$library.dayjs().format('D.MM.YY HH:mm:ss')
		}, 1000);
	},
	methods: {
		onChangeLog(to) {
			panel.open(`logs/${to}`);
		},
	},
	created() {
		console.log(this.content)
	}
};
</script>
