/**
 * RichEditor — brick-local TipTap wrapper.
 *
 * The host's @/components/ui/rich-editor offers a tiptap/tinymce switch; here we bundle
 * ONLY TipTap (TinyMCE would pull a large dependency the brick doesn't need). The `engine`
 * prop is still accepted for API compatibility with NewsFormPage (the tiptap/tinymce toggle
 * stays in the UI) but always renders TipTap.
 *
 * Controlled value: TipTap's useEditor only takes `content` as the INITIAL value, so we add
 * an effect that re-sets the document whenever `value` changes from the outside (e.g. the
 * language switch in NewsFormPage reloads the article in another locale) — without clobbering
 * the user's caret while they type (we skip when the incoming value already equals the editor
 * HTML).
 */
import { useEffect } from 'react'
import { useEditor, EditorContent } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import TextAlign from '@tiptap/extension-text-align'
import { Editor as TiptapEditor } from '@tiptap/core'
import {
  Bold, Italic, Underline as UnderlineIcon, Strikethrough,
  AlignLeft, AlignCenter, AlignRight, AlignJustify,
  Link as LinkIcon, List, ListOrdered, Heading2, Heading3,
  RemoveFormatting,
} from 'lucide-react'
import { cn } from '../../lib/utils'
import { t } from '../../lib/i18n'

// ─── Types ────────────────────────────────────────────────────────────────────

export type RichEditorEngine = 'tiptap' | 'tinymce'

interface RichEditorProps {
  value: string
  onChange: (html: string) => void
  placeholder?: string
  engine?: RichEditorEngine
  minRows?: number
  className?: string
}

// ─── Toolbar button ─────────────────────────────────────────────────────────

function ToolbarBtn({
  active, onClick, title, children,
}: {
  active?: boolean
  onClick: () => void
  title: string
  children: React.ReactNode
}) {
  return (
    <button
      type="button"
      title={title}
      onMouseDown={(e) => { e.preventDefault(); onClick() }}
      className={cn(
        'flex size-7 items-center justify-center rounded transition-colors',
        active
          ? 'bg-primary text-primary-foreground'
          : 'text-muted-foreground hover:bg-accent hover:text-foreground',
      )}
    >
      {children}
    </button>
  )
}

// ─── Toolbar ──────────────────────────────────────────────────────────────────

function Toolbar({ editor }: { editor: TiptapEditor }) {
  const setLink = () => {
    const prev = (editor.getAttributes('link').href as string) ?? ''
    const url = window.prompt('URL', prev)
    if (url === null) return
    if (url === '') {
      editor.chain().focus().extendMarkRange('link').unsetLink().run()
    } else {
      editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
    }
  }

  return (
    <div className="flex flex-wrap items-center gap-0.5 border-b border-border bg-muted/30 px-2 py-1.5">
      <ToolbarBtn active={editor.isActive('heading', { level: 2 })} onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()} title={t('h2')}>
        <Heading2 className="size-3.5" />
      </ToolbarBtn>
      <ToolbarBtn active={editor.isActive('heading', { level: 3 })} onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()} title={t('h3')}>
        <Heading3 className="size-3.5" />
      </ToolbarBtn>

      <div className="mx-1 h-4 w-px bg-border" />

      <ToolbarBtn active={editor.isActive('bold')} onClick={() => editor.chain().focus().toggleBold().run()} title={t('bold')}>
        <Bold className="size-3.5" />
      </ToolbarBtn>
      <ToolbarBtn active={editor.isActive('italic')} onClick={() => editor.chain().focus().toggleItalic().run()} title={t('italic')}>
        <Italic className="size-3.5" />
      </ToolbarBtn>
      <ToolbarBtn active={editor.isActive('underline')} onClick={() => editor.chain().focus().toggleUnderline().run()} title={t('underline')}>
        <UnderlineIcon className="size-3.5" />
      </ToolbarBtn>
      <ToolbarBtn active={editor.isActive('strike')} onClick={() => editor.chain().focus().toggleStrike().run()} title={t('strikethrough')}>
        <Strikethrough className="size-3.5" />
      </ToolbarBtn>

      <div className="mx-1 h-4 w-px bg-border" />

      <ToolbarBtn active={editor.isActive({ textAlign: 'left' })} onClick={() => editor.chain().focus().setTextAlign('left').run()} title={t('align_left')}>
        <AlignLeft className="size-3.5" />
      </ToolbarBtn>
      <ToolbarBtn active={editor.isActive({ textAlign: 'center' })} onClick={() => editor.chain().focus().setTextAlign('center').run()} title={t('align_center')}>
        <AlignCenter className="size-3.5" />
      </ToolbarBtn>
      <ToolbarBtn active={editor.isActive({ textAlign: 'right' })} onClick={() => editor.chain().focus().setTextAlign('right').run()} title={t('align_right')}>
        <AlignRight className="size-3.5" />
      </ToolbarBtn>
      <ToolbarBtn active={editor.isActive({ textAlign: 'justify' })} onClick={() => editor.chain().focus().setTextAlign('justify').run()} title={t('justify')}>
        <AlignJustify className="size-3.5" />
      </ToolbarBtn>

      <div className="mx-1 h-4 w-px bg-border" />

      <ToolbarBtn active={editor.isActive('bulletList')} onClick={() => editor.chain().focus().toggleBulletList().run()} title={t('bullet_list')}>
        <List className="size-3.5" />
      </ToolbarBtn>
      <ToolbarBtn active={editor.isActive('orderedList')} onClick={() => editor.chain().focus().toggleOrderedList().run()} title={t('numbered_list')}>
        <ListOrdered className="size-3.5" />
      </ToolbarBtn>

      <div className="mx-1 h-4 w-px bg-border" />

      <ToolbarBtn active={editor.isActive('link')} onClick={setLink} title={t('link')}>
        <LinkIcon className="size-3.5" />
      </ToolbarBtn>

      <ToolbarBtn active={false} onClick={() => editor.chain().focus().clearNodes().unsetAllMarks().run()} title={t('clear_formatting')}>
        <RemoveFormatting className="size-3.5" />
      </ToolbarBtn>
    </div>
  )
}

// ─── Public component ─────────────────────────────────────────────────────────

export function RichEditor({ value, onChange, placeholder, minRows = 6, className }: RichEditorProps) {
  const editor = useEditor({
    extensions: [
      // StarterKit v3 already bundles Underline + Link → don't add them again (duplicate
      // extension names crash the editor). Configure Link via StarterKit.
      StarterKit.configure({ link: { openOnClick: false } }),
      TextAlign.configure({ types: ['heading', 'paragraph'] }),
      Placeholder.configure({ placeholder: placeholder ?? t('editor_ph') }),
    ],
    content: value,
    onUpdate: ({ editor }) => onChange(editor.getHTML()),
    editorProps: {
      attributes: {
        class: cn(
          'prose prose-sm max-w-none focus:outline-none px-4 py-3 text-foreground',
          `min-h-[${minRows * 1.6}rem]`,
        ),
      },
    },
  })

  // Re-sync when the controlled value changes externally (e.g. language switch).
  // Skip if it already matches the editor's HTML to avoid resetting the caret on each keystroke.
  useEffect(() => {
    if (!editor) return
    const current = editor.getHTML()
    const next = value || '<p></p>'
    if (next !== current) {
      editor.commands.setContent(next, { emitUpdate: false })
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [value, editor])

  if (!editor) return null

  return (
    <div className={cn('rounded-xl border border-border bg-card focus-within:border-ring/40 focus-within:shadow-sm transition-shadow', className)}>
      <Toolbar editor={editor} />
      <EditorContent editor={editor} />
    </div>
  )
}
