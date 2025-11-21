# Admin Messaging System - Visual Guide

## Page Layout

The messaging page is divided into two main sections:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         ADMIN PANEL HEADER                              │
│  AlertDavao                                    [Admin Name] [Dropdown]  │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  Messages                                                               │
│  Communication and notifications center                                │
│                                                                         │
├────────────────────┬────────────────────────────────────────────────────┤
│                    │                                                    │
│  USERS LIST        │            CHAT INTERFACE                          │
│  (320px width)     │            (Flexible width)                        │
│                    │                                                    │
│  ┌──────────────┐  │  ┌──────────────────────────────────────────────┐ │
│  │ Users        │  │  │ John Smith                                   │ │
│  └──────────────┘  │  └──────────────────────────────────────────────┘ │
│                    │                                                    │
│  ┌──────────────┐  │  ┌──────────────────────────────────────────────┐ │
│  │ 👤 JS        │  │  │                                              │ │
│  │ John Smith   │◄─┼─►│  Hello, I need help    [09:15 AM]            │ │
│  │ john@ex.com  │  │  │                                              │ │
│  └──────────────┘  │  │               [Thanks for reaching out!]    │ │
│                    │  │               [09:16 AM]                     │ │
│  ┌──────────────┐  │  │                                              │ │
│  │ 👤 MD        │  │  │  Can you help me?      [09:17 AM]            │ │
│  │ Mary Doe     │  │  │                                              │ │
│  │ mary@ex.com  │  │  │               [Of course! What do you need?]│ │
│  └──────────────┘  │  │               [09:18 AM]                     │ │
│                    │  │                                              │ │
│  ┌──────────────┐  │  │  (Scrollable)                                │ │
│  │ 👤 RJ        │  │  │                                              │ │
│  │ Robert Jones │  │  └──────────────────────────────────────────────┘ │
│  │ robert@ex.com│  │                                                    │
│  └──────────────┘  │  ┌──────────────────────────────────────────────┐ │
│                    │  │                                              │ │
│  (Scrollable)      │  │  [Type a message...            ] [ 📤 Send ] │ │
│                    │  │                                              │ │
│                    │  └──────────────────────────────────────────────┘ │
│                    │                                                    │
└────────────────────┴────────────────────────────────────────────────────┘
```

## Color Scheme

### User List Panel
- **Background**: White (#ffffff)
- **Border**: Light gray (#e5e7eb)
- **Header Background**: Very light gray (#f9fafb)
- **User Card Hover**: Light gray (#f3f4f6)
- **Active User**: Light blue (#eff6ff) with blue border (#3b82f6)

### Chat Interface
- **Background**: White (#ffffff)
- **Header**: Light gray (#f9fafb)
- **Sent Messages**: Blue (#3b82f6) with white text
- **Received Messages**: Light gray (#f3f4f6) with dark text (#1f2937)
- **Input Border**: Light gray (#e5e7eb), blue on focus (#3b82f6)
- **Send Button**: Blue (#3b82f6), darker on hover (#2563eb)

## Component Details

### 1. User List Header
```
┌────────────────────┐
│ Users              │
└────────────────────┘
```
- Font size: 1rem
- Font weight: 600 (Semi-bold)
- Padding: 1.25rem
- Border bottom: 1px solid #e5e7eb

### 2. User Card (Default State)
```
┌────────────────────┐
│ 👤 JS              │
│ John Smith         │
│ john@example.com   │
└────────────────────┘
```
- Avatar: 40px circle with initials
- Name: 0.875rem, weight 500
- Email: 0.75rem, gray color
- Padding: 1rem
- Border radius: 8px

### 3. User Card (Active State)
```
┌────────────────────┐ ◄── Blue left border (3px)
│ 👤 JS              │
│ John Smith         │ ◄── Highlighted in light blue
│ john@example.com   │
└────────────────────┘
```
- Background: Light blue (#eff6ff)
- Border: 1px solid blue (#3b82f6)
- Left border: 3px solid blue

### 4. Chat Header
```
┌──────────────────────────────────────┐
│ John Smith                           │
└──────────────────────────────────────┘
```
- Font size: 1rem
- Font weight: 600
- Background: #f9fafb
- Padding: 1.25rem

### 5. Message Bubble (Received - Left Side)
```
┌────────────────────────┐
│ Hello, I need help     │
│ Oct 21, 9:15 AM       │
└────────────────────────┘
```
- Background: #f3f4f6 (light gray)
- Text color: #1f2937 (dark)
- Aligned: Left
- Max width: 70%
- Border radius: 12px (bottom-left: 4px)

### 6. Message Bubble (Sent - Right Side)
```
                ┌────────────────────────┐
                │ Thanks for reaching out│
                │ Oct 21, 9:16 AM       │
                └────────────────────────┘
```
- Background: #3b82f6 (blue)
- Text color: White
- Aligned: Right
- Max width: 70%
- Border radius: 12px (bottom-right: 4px)

### 7. Message Input Area
```
┌──────────────────────────────────────────────┐
│                                              │
│ [Type a message...                 ] [Send] │
│                                              │
└──────────────────────────────────────────────┘
```
- Background: #f9fafb
- Input: White with gray border
- Send button: Blue background, white text
- Input padding: 0.75rem 1rem
- Border radius: 8px

### 8. Empty State (No User Selected)
```
┌────────────────────────────────┐
│                                │
│           💬                   │
│                                │
│   Select a chat to proceed     │
│                                │
└────────────────────────────────┘
```
- Icon: 64px, light gray, 30% opacity
- Text: 1.125rem, gray color
- Centered vertically and horizontally

## Interaction States

### User Selection Flow
1. **Default**: No user selected
   - Chat shows "Select a chat to proceed"
   - Input area is hidden

2. **User Clicked**: User selected
   - User card highlights in blue
   - Chat header shows user name
   - Messages load and display
   - Input area appears
   - Auto-refresh starts (every 3 seconds)

### Message Sending Flow
1. **Typing**: User types in input
   - Textarea auto-expands (max 120px)
   
2. **Sending**: User presses Enter or clicks Send
   - Send button disables temporarily
   - Message sends via AJAX
   - Input clears
   - Conversation refreshes
   - New message appears in blue on right
   - Auto-scroll to bottom
   - Send button re-enables

### Scrolling Behavior
- **User List**: Scrolls independently when overflowing
- **Chat Messages**: Scrolls independently, auto-scrolls to bottom on new message
- **Custom Scrollbar**: 6px width, rounded, gray with hover effect

## Responsive Behavior

### Desktop (> 768px)
```
┌─────────┬──────────────────┐
│ Users   │   Chat           │
│ List    │   Interface      │
│ 320px   │   Flexible       │
└─────────┴──────────────────┘
```

### Mobile (< 768px)
```
┌────────────────────────────┐
│ Users List                 │
│ (Collapsed, max 300px)     │
├────────────────────────────┤
│                            │
│ Chat Interface             │
│ (Full width, min 400px)    │
│                            │
└────────────────────────────┘
```

## Typography

- **Main Title**: 1.5rem, weight 600
- **Subtitle**: 1rem, gray (#6b7280)
- **Section Headers**: 1rem, weight 600
- **User Names**: 0.875rem, weight 500
- **User Emails**: 0.75rem, gray
- **Messages**: 0.875rem, line height 1.5
- **Timestamps**: 0.7rem, 70% opacity

## Shadows and Effects

- **Container Shadow**: `0 2px 4px rgba(0, 0, 0, 0.1)`
- **Hover Effects**: Smooth 0.2s transitions
- **Focus Ring**: `0 0 0 3px rgba(59, 130, 246, 0.1)` on inputs
- **Border Radius**: 
  - Containers: 12px
  - Inputs/Buttons: 8px
  - User cards: 8px
  - Message bubbles: 12px (with 4px corner)

## Icons

All icons use SVG with 20x20px size in navigation:
- **Messages Icon**: Chat bubble (`M21 15a2 2 0 0 1-2 2H7l-4 4V5...`)
- **Send Icon**: Paper plane (`line + polygon`)
- **User Avatar**: Circular with initials

## Accessibility

- **Keyboard Navigation**: Tab through users, Enter to send
- **Focus States**: Clear blue focus rings
- **Color Contrast**: WCAG AA compliant
- **Screen Reader**: Proper semantic HTML
- **Touch Targets**: Minimum 44px for mobile

## Performance

- **Auto-refresh**: 3 seconds interval
- **Debounced Input**: Smooth textarea resizing
- **Efficient Queries**: Eager loading with relationships
- **Clean-up**: Intervals cleared on page leave

---

This visual guide provides a complete reference for the messaging system's appearance and behavior. The interface is modern, clean, and user-friendly, matching the overall admin panel design.
